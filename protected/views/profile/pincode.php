<h2>Pincode</h2>
<table class="standard">
    <tbody>
        <tr>
            <td>
                <div class="data-group flex-layout cols-full">
                    <div class="cols-2">
                        <label>Pincode:</label>
                    </div>
                    <div class="cols-5 js-pincode-txt">
                        <?=$user_auth && $user_auth->pincode ? $user_auth->pincode : 'Please contact the system admin to assign you a pincode';?>
                    </div>
                </div>
            </td>
        </tr>
    </tbody>
</table>