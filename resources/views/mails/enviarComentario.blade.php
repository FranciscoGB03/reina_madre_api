<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>{{trans('mails.comentario')}}</title>
</head>

<body yahoo bgcolor="#f6f8f1" style="margin: 0;padding: 0;min-width: 100% !important;">
    <table width="100%" bgcolor="#f6f8f1" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <!--[if (gte mso 9)|(IE)]>
                <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td>
                <![endif]-->
                <table bgcolor="#ffffff" .class="content" align="center" cellpadding="0" cellspacing="0" border="0"
                       style="width: 100%;max-width: 600px;">
                    <tr>
                        <td bgcolor="#204051" class="header" style="padding: 40px 30px 20px 30px;">
                            <table width="70" align="left" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td height="70" style="padding: 0 20px 20px 0;">
                                        <img class="fix" src="{{ $message->embed(public_path() . '/img/logo.png') }}"
                                             width="70" height="70" border="0"
                                             alt="" style="height: auto;">
                                    </td>
                                </tr>
                            </table>
                            <!--[if (gte mso 9)|(IE)]>
                            <table width="425" align="left" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                            <![endif]-->
                            <table class="col425" align="left" border="0" cellpadding="0" cellspacing="0"
                                   style="width: 100%; max-width: 425px;">
                                <tr>
                                    <td height="70">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td class="subhead"
                                                    style="padding: 0 0 0 3px;font-size: 15px;color: #ffffff;font-family: sans-serif;letter-spacing: 10px;">
                                                    Xheim
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="h1"
                                                    style="padding: 5px 0 0 0;color: #f0ece2;font-family: sans-serif;font-size: 33px;line-height: 38px;font-weight: bold;">
                                                    {{trans('mails.comentario')}}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!--[if (gte mso 9)|(IE)]>
                            </td>
                            </tr>
                            </table>
                            <![endif]-->
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="margin-top:10px; color: #153643;font-family: sans-serif;padding-left:15px;font-size: 24px;line-height: 28px;font-weight: bold;">
                                {{trans('mails.remitente')}}: {{$nombre}}
                            </p>
                            <p style="padding-left:15px; font-size: 20px;">{{trans('mails.correoRemitente')}}: {{$remitente}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="color: #153643;font-family: sans-serif;padding-left:15px;font-size: 24px;line-height: 28px;font-weight: bold;">
                                {{trans('mails.mensaje')}}:
                            </p>
                            <p style="padding-left:15px;font-size: 20px;">{{$body}}</p>
                        </td>
                    </tr>
                    @include('mails.footer')
                </table>
                <!--[if (gte mso 9)|(IE)]>
                </td>
                </tr>
                </table>
                <![endif]-->
            </td>
        </tr>
    </table>
</body>
</html>

