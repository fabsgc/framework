<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Mime.php
	 | @author : fab@c++
	 | @description : mime types
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Mime;

	class Mime{
		const ZIP                   = 'application/gzip'             ;
		const GZ                    = 'application/x-gzip'           ;
		const GZ_COMPRESSED         = 'application/x-zip-compressed' ;
		const PDF                   = 'application/pdf'              ;
		const JS                    = 'application/javascript'       ;
		const OGG                   = 'application/ogg'              ;
		const EXE                   = 'application/octet-stream'     ;
		const DOC                   = 'application/msword'           ;
		const XLS                   = 'application/vnd.ms-excel'     ;
		const PPT                   = 'application/vnd.ms-powerpoint';
		const NONE                  = 'application/force-download'   ;
		const XML                   = 'application/xml'              ;
		const FLASH                 = 'application/x-shockwave-flash';
		const JSON                  = 'application/json'             ;
		const PNG                   = 'image/png'                    ;
		const GIF                   = 'image/gif'                    ;
		const JPG                   = 'image/jpeg'                   ;
		const TIFF                  = 'image/tiff'                   ;
		const ICO                   = 'image/vnd.microsoft.icon'     ;
		const SVG                   = 'image/svg+xml'                ;
		const JPEG                  = 'image/jpeg'                   ;
		const TXT                   = 'text/plain'                   ;
		const HTM                   = 'text/html'                    ;
		const HTML                  = 'text/html'                    ;
		const CSV                   = 'text/csv'                     ;
		const MPEGAUDIO             = 'audio/mpeg'                   ;
		const MP3                   = 'audio/mpeg'                   ;
		const RPL                   = 'audio/vnd.rn-realaudio'       ;
		const WAV                   = 'audio/x-wav'                  ;
		const MPEG                  = 'video/mpeg'                   ;
		const MP4                   = 'video/mp4'                    ;
		const QUICKTIME             = 'video/quicktime'              ;
		const WMV                   = 'video/x-ms-wmv'               ;
		const AVI                   = 'video/x-msvideo'              ;
		const FLV                   = 'video/x-flv'                  ;
		const ODT                   = 'application/vnd.oasis.opendocument.text'                                ;
		const ODTCALC               = 'application/vnd.oasis.opendocument.spreadsheet'                         ;
		const ODTPRE                = 'application/vnd.oasis.opendocument.presentation'                        ;
		const ODTGRA                = 'application/vnd.oasis.opendocument.graphics'                            ;
		const XLS2007               = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'      ;
		const DOC2007               = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
		const XUL                   = 'application/vnd.mozilla.xul+xml'                                        ;
		const TAR                   = 'application/x-tar'                                                      ;
		const TGZ                   = 'application/x-tar'                                                      ;
	}