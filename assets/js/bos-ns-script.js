(function ($) {
    'use strict';

    var $submissions_wrapper = $('.badgeos-feedback-container');

    $(document).ready(function () {
        var BOSNS_front = {
            init: function () {

                var $body = $('body');

                // Retrieve feedback posts when an approriate action is taken
                $body.on('change', '.badgeos-feedback-filter select', BOSNS_front.badgeos_get_feedback);
                $body.on('change', '#badgeos_settings_submnomi_settings #submission_email', BOSNS_front.badgeos_subnom_show_hide_settings);


                $body.on('submit', '.badgeos-feedback-search-form', function (event) {
                    event.preventDefault();
                    BOSNS_front.badgeos_get_feedback();
                });

                // Hide comment form on feedback posts with toggle
                if ($('div.badgeos-feedback-container .badgeos-submissions').length) {
                    $submissions_wrapper = $('.badgeos-feedback-container');
                } else {
                    $submissions_wrapper = $('.badgeos-submissions');
                }

                BOSNS_front.badgeos_hide_submission_comments($submissions_wrapper);
                $submissions_wrapper.on('click', '.submission-comment-toggle', function (event) {
                    event.preventDefault();

                    var $button = $(this);
                    $button.parent().siblings('.badgeos-submission-comments-wrap').fadeIn('fast');
                    $button.parent().siblings('.badgeos-comment-form').fadeIn('fast');
                    $button.hide();
                    $button.parent().siblings('.badgeos-comment-form').fadeIn('fast');
                    var value = $button.parent().siblings('.badgeos-comment-form').find('.badgeos_comment').val();
                    if (value) {
                        alert('Comment is saved in draft mode.');
                    }
                });
                
                $body.on('click', '#submission_submit_button', function() {
                    $(this).hide();
                });

                // Approve/deny feedback
                $body.on('click', '.badgeos-feedback-buttons .button', function (event) {
                    event.preventDefault();
                    var $button = $(this);
                    $.ajax({
                        url: badgeos_feedback_buttons.ajax_url,
                        data: {
                            'action': 'update-feedback',
                            'status': $button.data('action'),
                            'feedback_id': $button.data('feedback-id'),
                            'feedback_type': $button.siblings('input[name=feedback_type]').val(),
                            'achievement_id': $button.siblings('input[name=achievement_id]').val(),
                            'user_id': $button.siblings('input[name=user_id]').val(),
                            'wpms': $button.siblings('input[name=wpms]').val(),
                            'nonce': $button.siblings('input[name=badgeos_feedback_review]').val(),
                        },
                        dataType: 'json',
                        success: function (response) {
                            $('.badgeos-feedback-response', $button.parent()).remove();
                            $(response.data.message).appendTo($button.parent()).fadeOut(3000);
                            $('.badgeos-feedback-' + $button.data('feedback-id') + ' .badgeos-feedback-status').html(response.data.status);
                            $('.cmb2-id--badgeos-submission-current .cmb-td, .cmb2-id--badgeos-nomination-current .cmb-td').html(response.data.status);
                            $('.badgeos-comment-date-by').html('<span class="badgeos-status-label">Status:</span> ' + response.data.status);

                            var feed_back_id = $button.data('feedback-id');
                            if ( response.data.status == 'Approved' ) {
                              $button.parent().hide();
                              $('.cmb2-id--badgeos-submission-update .cmb-th label').html('Reviewed');
                            }

                            if ( response.data.status == 'Denied' ) {
                              $button.parent().hide();
                              $('.cmb2-id--badgeos-submission-update .cmb-th label').html('Reviewed');
                            }

                            if (response.data.status != 'Approved') {
                                $('.comment-toggle-' + feed_back_id + '').show();
                            } else {
                                $('.comment-toggle-' + feed_back_id + '').hide();
                            }

                        }
                    });
                });

                BOSNS_front.badgeos_subnom_show_hide_settings();
            },
            /**
             * Admin Script
             */
            badgeos_hide_submission_comments: function (submissions_wrapper) {
                submissions_wrapper.find('.badgeos-submission-comments-wrap').hide();
                submissions_wrapper.find('.badgeos-comment-form').hide();
                //submissions_wrapper.find( '.submission-comment-toggle' ).show();
            },

            /**
             * show hide the submission and nominations
             */
            badgeos_subnom_show_hide_settings: function () {
                var this_element = $('#badgeos_settings_submnomi_settings #submission_email');
                if (this_element.val() == 'disabled') {
                    $('.badgeos_subnom_email_addresses_cls').css('display', 'none');
                } else {
                    $('.badgeos_subnom_email_addresses_cls').css('display', 'block');
                }
            },

            /**
             * Get feedback posts
             */
            badgeos_get_feedback: function () {

                $('.badgeos-spinner').show();

                BOSNS_front.badgeos_setup_feedback_filters();

                $.ajax({
                    url: badgeos_feedback.ajax_url,
                    data: badgeos_feedback,
                    dataType: 'json',
                    success: function (response) {
                        $('.badgeos-spinner').hide();
                        $submissions_wrapper.html(response.data.feedback);
                        BOSNS_front.badgeos_hide_submission_comments($submissions_wrapper);
                    },
                    error: function (x, t, m) {
                        if (window.console) {
                            console.log([t, m]);
                        }
                    }
                });
            },
            /**
             * Setup feedback filters
             */
            badgeos_setup_feedback_filters: function () {
                if ('undefined' != typeof badgeos_feedback.filters && badgeos_feedback.filters) {
                    for (var field_selector in badgeos_feedback.filters) {
                        if ('' !== badgeos_feedback.filters[field_selector]) {
                            badgeos_feedback[field_selector] = $(badgeos_feedback.filters[field_selector]).val();
                        }
                    }
                }
            }
        };

        BOSNS_front.init();
    });
})(jQuery);