<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.shierd.com
 * @since      1.0.0
 *
 * @package    Ai_Companion
 * @subpackage Ai_Companion/public/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="aic chat-page">
    <svg style="position:absolute;top:-10000px;left:-10000px">
        <defs id="svg-defs">
            <symbol id="message-tail-filled" viewBox="0 0 11 20"><g transform="translate(9 -14)" fill="inherit" fill-rule="evenodd"><path d="M-6 16h6v17c-.193-2.84-.876-5.767-2.05-8.782-.904-2.325-2.446-4.485-4.625-6.48A1 1 0 01-6 16z" transform="matrix(1 0 0 -1 0 49)" id="corner-fill" fill="inherit"></path></g></symbol>
        </defs>
    </svg>
    <div class="chats-container">
        <div class="chat">
            <div class="chat-background"><div class="chat-background-item is-pattern is-dark is-visible">
                <!-- <canvas width="50" height="50" data-colors="#fec496,#dd6cb9,#962fbf,#4f5bd5" class="chat-background-item-canvas chat-background-item-color-canvas" style="--opacity-max:0.3;"></canvas><canvas data-original-height="768" width="946" height="1152" class="chat-background-item-canvas chat-background-item-pattern-canvas"></canvas> -->
            </div></div>
            <div class="bubbles has-groups has-sticky-dates">
                <div class="scrollable scrollable-y">
                    <div class="bubbles-inner has-rights is-chat is-channel">
                        <section class="bubbles-date-group">
                            
                        </section>
                    </div>
                </div>
            </div>
            <div class="chat-input">
                <div class="chat-input-container">
                    <div class="rows-wrapper-wrapper">
                        <div class="rows-wrapper chat-input-wrapper">
                            <svg viewBox="0 0 11 20" width="11" height="20" class="bubble-tail"><use href="#message-tail-filled"></use></svg>
                            <div class="new-message-wrapper">
                                <button class="btn-icon tgico-none toggle-emoticons"></button>
                                <div class="input-message-container">
                                    <div class="input-message-input i18n scrollable scrollable-y no-scrollbar" contenteditable="true" dir="auto" data-placeholder="Message" style="transition-duration: 181ms; height: 37px;" data-peer-id="1594407922"></div>
                                    <!-- <div contenteditable="true" tabindex="-1" class="input-message-input i18n scrollable scrollable-y no-scrollbar input-field-input-fake"></div> -->
                                </div>
                                <button class="btn-icon tgico-scheduled btn-clean float hide show"></button>
                                <!-- <button class="btn-icon tgico-botcom toggle-reply-markup float show"></button> -->
                            </div>
                        </div>
                    </div>
                    <div class="btn-send-container">
                        <div class="record-ripple"></div>
                        <button class="btn-icon tgico-none btn-circle btn-send animated-button-icon rp record">
                        <span style="width: 28px; height: 28px;"><svg t="1676789220773" class="icon" viewBox="0 0 1117 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="4140" width="28" height="28" fill="currentColor"><path d="M53.865 558.08l289.92 121.6 560-492.16-491.52 530.56 371.84 140.8c8.96 3.2 19.2-1.28 22.4-10.24V848l260.48-816.64-1014.4 494.72c-8.96 4.48-12.16 14.72-8.32 23.68 2.56 3.84 5.76 7.04 9.6 8.32z m357.76 434.56l144.64-155.52-144.64-58.88v214.4z" p-id="4141"></path></svg></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const BASE_API = "<?php global $wp_rewrite; echo $wp_rewrite->using_permalinks() ? '/'.rest_get_url_prefix() : '/index.php?rest_route='; ?>"
</script>