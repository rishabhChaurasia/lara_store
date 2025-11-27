<?php

return [

    /*
     * Enable / disable Google2FA.
     */
    'enabled' => env('Google2FA_enabled', true),

    /*
     * Lifetime in minutes.
     *
     * In case you need to set the lifetime of the 2FA feature,
     * you can do it here setting the minutes.
     *
     * To immediate destroy, set to 0.
     * To never expires, set to -1.
     */
    'lifetime' => env('Google2FA_lifetime', 0), // 0 = until reset / -1 = forever / x = minutes

    /*
     * The maximum offset allowed per use.
     *
     * Defaults to 1 to prevent large offset usage.
     */
    'window' => env('Google2FA_window', 1),

    /*
     * The length of the Google2FA secret.
     *
     * The length of the secret can be between 99 and 1024 characters.
     * However, it is recommended to keep the length to 16 characters for compatibility.
     * 
     * According to RFC 4226, the secret should be 160 bits long (20 characters).
     * This is the default value.
     */
    'secret_length' => 16,

    /*
     * The name of the Google2FA input field.
     */
    'input_name' => 'one_time_password',

    /*
     * The name of the Google2FA session field.
     */
    'session_name' => 'google2fa', // deprecated
    
    /*
     * The name of the Google2FA cookie field.
     */
    'cookie_name' => 'google2fa', // deprecated

    /*
     * Enable / disable the use of cookies to store the secret.
     */
    'use_cookie' => false, // deprecated

    /*
     * Forbid multiple authentications for the same key within the window period.
     * 
     * This prevents multiple authentications for the same key within the window period.
     * 
     * If you set this to true, the user will not be able to authenticate again within the window period.
     */
    'forbid_multiple_auth_attempts_within_window' => false, // deprecated

    /*
     * The message shown when the secret key has been compromised.
     */
    'forbidden_message' => 'It is not possible to authenticate with this key at the moment.', // deprecated

    /*
     * The number of digits in the one-time password.
     */
    'digits' => 6,

    /*
     * The number of seconds between key regeneration.
     */
    'period' => 30,

    /*
     * The algorithm used to generate the codes.
     * 
     * It must be one of the following:
     * - 'SHA1' (default)
     * - 'SHA256'
     * - 'SHA512'
     */
    'algorithm' => 'SHA1',

    /*
     * QR Code backend to use.
     *
     * Defaults to null, which uses the BaconQrCode backend.
     *
     * Possible values:
     * - null (BaconQrCode)
     * - 'svg'
     * - 'eps'
     */
    'qrcode_backend' => null,

    /*
     * The image backend for BaconQrCode.
     *
     * Possible values:
     * - BaconQrCode\Renderer\Image\SvgImageBackEnd::class (default)
     * - BaconQrCode\Renderer\Image\PngImageBackEnd::class
     */
    'image_backend' => BaconQrCode\Renderer\Image\SvgImageBackEnd::class,

];