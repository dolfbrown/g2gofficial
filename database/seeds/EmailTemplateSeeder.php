<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('email_templates')->insert([
            [
                'name' => 'Welcome User',
                'type' => 'HTML',
                'position' => 'Content',
                'sender_email' => 'support@domain.com',
                'sender_name' => Null,
                'subject' => 'Welcome to {shop_name}',
                'body' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                  <meta name="viewport" content="width=device-width" />
                  <title>Airmail Welcome</title>
                </head>
                <body bgcolor="#ffffff">
                  <div align="center">
                    <table class="head-wrap w320 full-width-gmail-android" bgcolor="#f9f8f8" cellpadding="0" cellspacing="0" border="0" width="100%">
                      <tr>
                        <td background="https://www.filepicker.io/api/file/UOesoVZTFObSHCgUDygC" bgcolor="#ffffff" width="100%" height="8" valign="top">
                          <!--[if gte mso 9]>
                          <v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="mso-width-percent:1000;height:8px;">
                            <v:fill type="tile" src="https://www.filepicker.io/api/file/UOesoVZTFObSHCgUDygC" color="#ffffff" />
                            <v:textbox inset="0,0,0,0">
                          <![endif]-->
                          <div height="8">
                          </div>
                          <!--[if gte mso 9]>
                            </v:textbox>
                          </v:rect>
                          <![endif]-->
                        </td>
                      </tr>
                      <tr class="header-background">
                        <td class="header container" align="center">
                          <div class="content">
                            <span class="brand">
                              <a href="#">
                                Company Name
                              </a>
                            </span>
                          </div>
                        </td>
                      </tr>
                    </table>

                    <table class="body-wrap w320">
                      <tr>
                        <td></td>
                        <td class="container">
                          <div class="content">
                            <table cellspacing="0">
                              <tr>
                                <td>
                                  <table class="soapbox">
                                    <tr>
                                      <td class="soapbox-title">Welcome to {platform_name}</td>
                                    </tr>
                                  </table>
                                  <table class="status-container single">
                                    <tr>
                                      <td class="status-padding"></td>
                                      <td>
                                        <table class="status" bgcolor="#fffeea" cellspacing="0">
                                          <tr>
                                            <td class="status-cell">
                                              Coupon code: <b>13448278949</b>
                                            </td>
                                          </tr>
                                        </table>
                                      </td>
                                      <td class="status-padding"></td>
                                    </tr>
                                  </table>
                                  <table class="body">
                                    <tr>
                                      <td class="body-padding"></td>
                                      <td class="body-padded">
                                        <div class="body-title">Hey {{ first_name }}, thanks for signing up</div>
                                        <table class="body-text">
                                          <tr>
                                            <td class="body-text-cell">
                                              We\'re really excited for you to join our community! You\'re just one click away from activate your account.
                                            </td>
                                          </tr>
                                        </table>
                                        <div style="text-align:left;"><!--[if mso]>
                                          <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="#" style="height:38px;v-text-anchor:middle;width:190px;" arcsize="11%" strokecolor="#407429" fill="t">
                                            <v:fill type="tile" src="https://www.filepicker.io/api/file/N8GiNGsmT6mK6ORk00S7" color="#41CC00" />
                                            <w:anchorlock/>
                                            <center style="color:#ffffff;font-family:sans-serif;font-size:17px;font-weight:bold;">Come on back</center>
                                          </v:roundrect>
                                        <![endif]--><a href="#"
                                        style="background-color:#41CC00;background-image:url(https://www.filepicker.io/api/file/N8GiNGsmT6mK6ORk00S7);border:1px solid #407429;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:17px;font-weight:bold;text-shadow: -1px -1px #47A54B;line-height:38px;text-align:center;text-decoration:none;width:190px;-webkit-text-size-adjust:none;mso-hide:all;">Activate Account!</a></div>
                                        <table class="body-signature-block">
                                          <tr>
                                            <td class="body-signature-cell">
                                              <p>Thanks so much,</p>
                                              <p class="body-signature"><img src="https://www.filepicker.io/api/file/2R9HpqboTPaB4NyF35xt" alt="Company Name"></p>
                                            </td>
                                          </tr>
                                        </table>
                                      </td>
                                      <td class="body-padding"></td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                            </table>
                          </div>
                        </td>
                        <td></td>
                      </tr>
                    </table>

                    <table class="footer-wrap w320 full-width-gmail-android" bgcolor="#e5e5e5">
                      <tr>
                        <td class="container">
                          <div class="content footer-lead">
                            <a href="#"><b>Get in touch</b></a> if you have any questions or feedback
                          </div>
                        </td>
                      </tr>
                    </table>
                    <table class="footer-wrap w320 full-width-gmail-android" bgcolor="#e5e5e5">
                      <tr>
                        <td class="container">
                          <div class="content">
                            <a href="#">Contact Us</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                            <span class="footer-group">
                              <a href="#">Facebook</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                              <a href="#">Twitter</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                              <a href="#">Support</a>
                            </span>
                          </div>
                        </td>
                      </tr>
                    </table>
                  </div>

                </body>
                </html>
                ',
                'created_at' => Carbon::Now(),
                'updated_at' => Carbon::Now(),
            ],[
                'name' => 'Welcome Customer',
                'type' => 'HTML',
                'position' => 'Content',
                'sender_email' => 'support@domain.com',
                'sender_name' => Null,
                'subject' => 'Welcome to {platform_name}',
                'body' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width" />
  <title>Airmail Welcome</title>
</head>
<body bgcolor="#ffffff">
  <div align="center">
    <table class="head-wrap w320 full-width-gmail-android" bgcolor="#f9f8f8" cellpadding="0" cellspacing="0" border="0" width="100%">
      <tr>
        <td background="https://www.filepicker.io/api/file/UOesoVZTFObSHCgUDygC" bgcolor="#ffffff" width="100%" height="8" valign="top">
          <!--[if gte mso 9]>
          <v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="mso-width-percent:1000;height:8px;">
            <v:fill type="tile" src="https://www.filepicker.io/api/file/UOesoVZTFObSHCgUDygC" color="#ffffff" />
            <v:textbox inset="0,0,0,0">
          <![endif]-->
          <div height="8">
          </div>
          <!--[if gte mso 9]>
            </v:textbox>
          </v:rect>
          <![endif]-->
        </td>
      </tr>
      <tr class="header-background">
        <td class="header container" align="center">
          <div class="content">
            <span class="brand">
              <a href="#">
                Company Name
              </a>
            </span>
          </div>
        </td>
      </tr>
    </table>

    <table class="body-wrap w320">
      <tr>
        <td></td>
        <td class="container">
          <div class="content">
            <table cellspacing="0">
              <tr>
                <td>
                  <table class="soapbox">
                    <tr>
                      <td class="soapbox-title">Welcome to {platform_name}</td>
                    </tr>
                  </table>
                  <table class="status-container single">
                    <tr>
                      <td class="status-padding"></td>
                      <td>
                        <table class="status" bgcolor="#fffeea" cellspacing="0">
                          <tr>
                            <td class="status-cell">
                              Coupon code: <b>13448278949</b>
                            </td>
                          </tr>
                        </table>
                      </td>
                      <td class="status-padding"></td>
                    </tr>
                  </table>
                  <table class="body">
                    <tr>
                      <td class="body-padding"></td>
                      <td class="body-padded">
                        <div class="body-title">Hey {{ first_name }}, thanks for signing up</div>
                        <table class="body-text">
                          <tr>
                            <td class="body-text-cell">
                              We\'re really excited for you to join our community! You\'re just one click away from activate your account.
                            </td>
                          </tr>
                        </table>
                        <div style="text-align:left;"><!--[if mso]>
                          <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="#" style="height:38px;v-text-anchor:middle;width:190px;" arcsize="11%" strokecolor="#407429" fill="t">
                            <v:fill type="tile" src="https://www.filepicker.io/api/file/N8GiNGsmT6mK6ORk00S7" color="#41CC00" />
                            <w:anchorlock/>
                            <center style="color:#ffffff;font-family:sans-serif;font-size:17px;font-weight:bold;">Come on back</center>
                          </v:roundrect>
                        <![endif]--><a href="#"
                        style="background-color:#41CC00;background-image:url(https://www.filepicker.io/api/file/N8GiNGsmT6mK6ORk00S7);border:1px solid #407429;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:17px;font-weight:bold;text-shadow: -1px -1px #47A54B;line-height:38px;text-align:center;text-decoration:none;width:190px;-webkit-text-size-adjust:none;mso-hide:all;">Activate Account!</a></div>
                        <table class="body-signature-block">
                          <tr>
                            <td class="body-signature-cell">
                              <p>Thanks so much,</p>
                              <p class="body-signature"><img src="https://www.filepicker.io/api/file/2R9HpqboTPaB4NyF35xt" alt="Company Name"></p>
                            </td>
                          </tr>
                        </table>
                      </td>
                      <td class="body-padding"></td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </div>
        </td>
        <td></td>
      </tr>
    </table>

    <table class="footer-wrap w320 full-width-gmail-android" bgcolor="#e5e5e5">
      <tr>
        <td class="container">
          <div class="content footer-lead">
            <a href="#"><b>Get in touch</b></a> if you have any questions or feedback
          </div>
        </td>
      </tr>
    </table>
    <table class="footer-wrap w320 full-width-gmail-android" bgcolor="#e5e5e5">
      <tr>
        <td class="container">
          <div class="content">
            <a href="#">Contact Us</a>&nbsp;&nbsp;|&nbsp;&nbsp;
            <span class="footer-group">
              <a href="#">Facebook</a>&nbsp;&nbsp;|&nbsp;&nbsp;
              <a href="#">Twitter</a>&nbsp;&nbsp;|&nbsp;&nbsp;
              <a href="#">Support</a>
            </span>
          </div>
        </td>
      </tr>
    </table>
  </div>

</body>
</html>
',
                'created_at' => Carbon::Now(),
                'updated_at' => Carbon::Now(),
            ]

        ]);
    }
}