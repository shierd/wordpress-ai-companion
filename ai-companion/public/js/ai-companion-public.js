(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	class AICompanion {
		constructor () {

		}

		on(selector, event, child, data, func) {
			if (typeof func == 'undefined') {
				func = child
				child = undefined
			}
			$(selector).on(event, child, data, (e) => {
				this[func](e.target, e)
			})
		}

		init() {
			this.on('.aic .chat .input-message-input', 'keyup', 'onInputMessageChange')
			this.on('.aic .chat .chat-input .btn-send', 'click', 'onButtonSendClick')
			this.on('.aic .chat', 'click', '.bubble .bubble-content .btn-copy', null, 'onButtonCopyClick')
			this.loadOldMessage()
		}

		loadOldMessage() {
			$.ajax({
				url: BASE_API + '/ai_companion/messages',
				type: 'GET',
				success: (res) => {
					let list = res.data ? res.data.list : []
					list.forEach(item => {
						if (item.role == 'user') {
							this.addMessage(item.content, true)
						} else if (item.role == 'assistant') {
							this.addMessage(item.content, false)
						}
					});
					this.highlightAll()
				},
				error: (err) => {
					console.error("loadOldMessage error: ", err)
				}
			})
		}

		highlightAll() {
			if (typeof hljs != 'undefined') {
				hljs.highlightAll()
			}
		}

		onInputMessageChange(target, e) {
			let message = $(target).text()
			// shift + enter 换行
			if (e.which === 13 && e.shiftKey) {
				e.preventDefault();
				let docFragment = document.createDocumentFragment()

				// add empty node
				let newEle = document.createTextNode('')
				docFragment.appendChild(newEle)

				// add a new line
				// let newEle = document.createTextNode('\n')
				// docFragment.appendChild(newEle)

				// add the br, or p, or something else
				// newEle = document.createElement('br')
				// docFragment.appendChild(newEle)

				// make the br replace selection
				let select = document.getSelection()
				let range = select.getRangeAt(0)
				range.deleteContents()
				range.insertNode(docFragment)
				
				// create a new range
				range = document.createRange()
				range.setStartAfter(newEle)
				range.collapse(true)

				// make the cursor there
				select.removeAllRanges()
				select.addRange(range)

				// 更改输入框高度
				$(target).animate({height: '+=21px'}, 21)
				return
			}
			// 回车发送
			if (e.which === 13 && !e.shiftKey) {
				this.sendMessage(message)
			}
			this.updateMessageInput()
		}

		onButtonSendClick() {
			let message = $('.aic .chat-input .rows-wrapper .input-message-input').text()
			this.sendMessage(message)
			this.updateMessageInput()
		}

		onButtonCopyClick(target) {
			let msgHTML = $.parseHTML($(target).siblings('.message').html())
			msgHTML.pop()
			msgHTML.pop()
			let message = $(msgHTML).text()
			if (window.isSecureContext) {
				navigator.clipboard.writeText(message).then(() => {
					$(target).text('copied')
					$(target).one('mouseout', function(e) {
						$(e.target).text('copy')
					})
				}).catch((err) => {
					console.error('Failed to copy', err)
				})
			}
		}

		updateMessageInput() {
			let input = $('.aic .chat-input .rows-wrapper .input-message-input')
			let message = input.text()
			if (message) {
				input.attr("data-placeholder", "")
			} else {
				input.css('height', '37px')
				input.attr("data-placeholder", "Message")
			}
		}

		sendMessage(message) {
			if (!message) {
				return
			}
			this.addMessage(message, true)
			let msgId = this.loadingMessage()
			$.ajax({
				url: BASE_API + '/ai_companion/answer',
				type: 'POST',
				data: {message},
				success: (res) => {
					let data = res.data
					this.loadMessage(msgId, data.text, data.time)
					this.highlightAll()
				},
				error: (err) => {
					let errMsg = 'ERROR: ' + (err.responseJSON.message || 'Server Exception!')
					this.loadMessage(msgId, errMsg, this.getTime(), true)
				}
			})
			$('.aic .chat-input .rows-wrapper .input-message-input').text("")
		}

		getMessageDom(message, time, is_out, is_loading) {
			let class_out = is_out ? 'is-out' : 'is-in'
			let class_loading = is_loading ? ' is-loading' : ''
			let data_message_id = is_loading ? message : ''
			message = is_loading ? '' : message
			let tpl = '' +
				'<div class="bubbles-group">' +
				'<div data-message-id="'+data_message_id+'" class="bubble hide-name '+class_out+' can-have-tail is-group-first is-group-last'+class_loading+'">' +
				'<div class="bubble-content-wrapper">' +
				'<div class="bubble-content">' +
					'<div class="message spoilers-container" dir="auto">' +
					message +
					'<span class="time tgico"><span class="i18n" dir="auto">'+time+'</span><div class="inner tgico"><span class="i18n" dir="auto">'+time+'</span></div></span>' +
					'<div class="loading-bounce"><div class="bounce"></div><div class="bounce"></div><div class="bounce"></div></div>' +
					'</div>' +
					'<svg viewBox="0 0 11 20" width="11" height="20" class="bubble-tail"><use href="#message-tail-filled"></use></svg>' +
					'<span class="btn-copy">copy</span>' +
				'</div>' +
				'</div>' +
				'</div>' +
				'</div>'
			return tpl
		}
		
		getTime() {
			let date = new Date()
			let hour = date.getHours()
			let min = date.getMinutes()
			return (hour < 10 ? '0'+hour : ''+hour) + ':' + (min < 10 ? '0'+min : ''+min)
		}

		addMessage(message, is_out) {
			let msgDom = this.getMessageDom(message, this.getTime(), is_out)
			$('.aic .chat .bubbles .bubbles-date-group').append(msgDom)
			this.scrollBottom()
		}

		loadingMessage() {
			let msgId = Date.now()
			let msgDom = this.getMessageDom(msgId, this.getTime(), false, true)
			$('.aic .chat .bubbles .bubbles-date-group').append(msgDom)
			this.scrollBottom()
			return msgId
		}

		loadMessage(message_id, message, time, is_error) {
			let messageBubble = $('.bubbles-group .bubble[data-message-id="'+message_id+'"]')
			messageBubble.removeClass('is-loading')
			if (is_error) {
				messageBubble.addClass('is-error')
			}
			let messageHTML = messageBubble.find('.bubble-content .message').html()
			messageHTML = message + messageHTML
			messageBubble.find('.bubble-content .message').html(messageHTML)
			this.scrollBottom()
		}

		scrollBottom() {
			$('.aic .chat .bubbles>.scrollable.scrollable-y').scrollTop($('.aic .chat .bubbles>.scrollable.scrollable-y').prop('scrollHeight'))
		}
	}

	$(function() {
		// 非插件页面
		if (!$('.aic.chat-page').length) {
			return
		}
		/**
		 * load script
		 * @param {string} src script url
		 * @param {function} callback callback when loading is complete
		 */
		function loadScript(src, callback) {
            var _script = document.createElement('script');
            _script.async = true;
            _script.src = src;
            _script.addEventListener('load', function () {
				_script.parentNode.removeChild(_script);
				_script.removeEventListener('load', null, false);
                typeof callback == 'function' && callback();
            }, false);
            document.body.appendChild(_script);
        }
		/**
		 * load style
		 * @param {string} src style url
		 */
		function loadStyle(src) {
			var link = document.createElement("link");
			link.rel = "stylesheet";
			link.type = "text/css";
			link.href = src;
			var head = document.getElementsByTagName("head")[0];
			head.appendChild(link);
		}
		// check if highlight plugin is not loaded
		if (typeof hljs === 'undefined') {
			loadScript('/wp-content/plugins/ai-companion/public/js/highlight.min.js')
			loadStyle('/wp-content/plugins/ai-companion/public/css/highlight-github-dark-dimmed.min.css')
		}
		// 初始化插件
		let aic = new AICompanion()
		aic.init()
		// 设置窗口高度
		function setWindowHeight() {
			let aicOffsetTop = $('.aic.chat-page').offset().top
			let windowHeight = window.innerHeight
			let aicHeight = windowHeight - aicOffsetTop
			$('.aic.chat-page').animate({height: aicHeight}, 200, () => {aic.scrollBottom()})
		}
		setWindowHeight()
		window.onresize = function () {
			setWindowHeight()
		}

		console.log("Hello AI Companion - by shier \n (https://www.shierd.com)")
	})

})( jQuery );
