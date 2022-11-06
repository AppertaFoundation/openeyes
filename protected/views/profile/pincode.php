<h2>Pincode</h2>
<div>
    <div class="alert-box info">
        <ul>
            <li>Your password verification will be expired in 30 seconds or immediately after page refresh</li>
            <li>You are not able to regenerate the pincode in the last 5 seconds before your password verification expiry</li>
            <li class="js-regen-status"><?=$pin_regen_status?></li>
        </ul>
    </div>
</div>
<table class="standard js-profile-pincode-layout">
    <tbody>
        <tr>
            <td class="js-pincode-label">
                <label>Pincode:</label>
            </td>
            <td>
                <?php if ($is_local_auth) { ?>
                    <div class="js-pincode-content">
                        <input type="password" name="user_pwd" id="user_pwd" placeholder="Enter Your Password">
                    </div>
                    <div class="js-pincode-actions">
                        <button class="button large hint green" id="js-view-pincode">View Pincode</button>
                    </div>
                <?php } else { ?>
                    <div class="js-pincode-content"></div>
                    <div class="js-pincode-actions">
                        <button class="button large hint green" id="js-view-pincode">click here to reveal PIN</button>
                    </div>
                <?php } ?>
            </td>
        </tr>
    </tbody>
    <i class="spinner" title="Loading..." style="display: none;"></i>
</table>
<script>
    $(function () {
        const ctn_selector = '.oe-user-profile .oe-full-main';
        const view_pin_btn_selector = `${ctn_selector} #js-view-pincode`;
        const regen_pin_btn_selector = `${ctn_selector} #js-regen-pincode`;
        const layout_table_selector = '.js-profile-pincode-layout';
        const pincode_content_ctn_selector = '.js-pincode-content';

        const $spinner = $(`${ctn_selector} .spinner`);

        // binding view pincode button
        $(document).off('click', view_pin_btn_selector).on('click', view_pin_btn_selector, function(e){
            e.preventDefault();

            const $pwd = $('#user_pwd');

            const $btn = $(this);

            const $alert_box = $(layout_table_selector).siblings('.alert-box');
            // if there is an existing alert box, remove it before rendering a new one
            if($alert_box.length){
                $alert_box.remove();
            }
            $pwd.removeClass('highlighted-error error');
            if( !(auth_source === 'OIDC' || auth_source === 'SAML') && !$pwd.val()){
                $pwd.addClass('highlighted-error error');
                return;
            }

            $spinner.show();
            $btn.prop('disabled', true);

            $.ajax({
                'url': '/profile/viewpincode',
                'type': 'POST',
                'data': {
                    YII_CSRF_TOKEN,
                    'pwd': $pwd.val(),
                },
                success: function(resp){
                    $(resp['msg']).insertBefore($(layout_table_selector))
                    if(resp['is_verified']){
                        startPwdCountDown($btn, resp['pincode_html'], resp['pincode_regen_html']);
                        $('.js-pincode-label').append(resp['info_icon']);
                    }
                },
                error: errorHandling,
                complete: function(){
                    $spinner.hide();
                    $btn.prop('disabled', false);
                    $pwd.val('');
                }
            });
        });

        // timer for regenerate button
        let re_enable_btn_countdown = null;
        // timer for password verification
        let pwd_expire_countdown = null;
        // binding generate pincode button
        $(document).off('click', regen_pin_btn_selector).on('click', regen_pin_btn_selector, function(e){
            e.preventDefault();
            const $pincode_ele = $(pincode_content_ctn_selector).find('.js-pincode');
            const $alert_box = $(layout_table_selector).siblings('.alert-box');
            // if there is an existing alert box, remove it before rendering a new one
            if($alert_box.length){
                $alert_box.remove();
            }
            const $btn = $(this);
            $spinner.show();
            $btn.prop('disabled', true);
            regenerateCountDown($btn);
            $.ajax({
                'url': '/profile/generatepincode',
                'type': 'get',
                success: function(resp){
                    $(resp['msg']).insertBefore($(layout_table_selector));
                    $('.js-regen-status').text(resp['pin_regen_status']);
                    if(resp['pincode_regen_html']){
                        $(resp['pincode_regen_html']).insertAfter($btn);
                        $btn.remove();
                    }
                    $pincode_ele.text(resp['pincode']);
                },
                error: errorHandling,
                complete: function(){
                    $spinner.hide();
                }
            });
        });

        // callback to start passwrod verification timer
        let startPwdCountDown = function($btn, pincode_html, regen_pin_btn_html){
            const $parent = $btn.closest('td');
            const $content = $parent.find(pincode_content_ctn_selector);
            $content.html(pincode_html);
            $parent.find('.js-pincode-actions').html(regen_pin_btn_html);
            if(pwd_expire_countdown){
                clearInterval(pwd_expire_countdown);
            }
            let time_to_expire = 30;
            const $count_down_ele = $content.find('.js-count-down');
            pwd_expire_countdown = setInterval(() => {
                // if it is 5 seconds before expiry, the regenerate button will be disabled
                if(time_to_expire < 5){
                    if(re_enable_btn_countdown){
                        clearInterval(re_enable_btn_countdown);
                    }
                    $(`${ctn_selector} button`).prop('disabled', true);
                }
                if(time_to_expire < 0){
                    location.reload();
                    return;
                }
                $count_down_ele.text(` (${time_to_expire})`);
                time_to_expire--;
            }, 1000);
        }

        // callback to start regenerate button timer
        let regenerateCountDown = function($btn){
            const btn_txt = $btn.text();
            let time_to_enable = 5;
            if(re_enable_btn_countdown){
                clearInterval(re_enable_btn_countdown);
            }
            re_enable_btn_countdown = setInterval(() => {
                if(time_to_enable < 0){
                    $btn.prop('disabled', false);
                    $btn.text(btn_txt);
                    return;
                }
                $btn.text(`${btn_txt} (${time_to_enable})`);
                time_to_enable--;
            }, 1000);
        }

        let errorHandling = function(XMLHttpRequest, textStatus, errorThrown){
            $(ctn_selector).find('.alert-box.warning, .alert-box.success').remove();
            const $table = $(ctn_selector).find(layout_table_selector);
            $(`<div class="alert-box warning">${errorThrown}, Please contact support or try again later</div>`).insertBefore($table);
        }
    });
</script>