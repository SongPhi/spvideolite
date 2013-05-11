var SPVideoClass = function(_baseUrl, _loadingEl, _detailElement) {
	this.baseUrl = _baseUrl;
	this.loadingElement = _loadingEl;
	this.detailElement = _detailElement;
	this.checkClipUrl = this.baseUrl + '/ajax_get_clip';

	this.handleCheckClipResponse = function(data) {
		//TODO: code here
		console.log(data);
		
		// hide loadingel, show detailel
		this.detailElement.css('display','block');
		this.loadingElement.css('display','none');

		// display response
		this.detailElement.html( '' );
		if (!data.error) {
			this.detailElement.html( this.base64_decode(data.formHtml) );
		}
	}

	this.checkClipFromElement = function(inputHandle) {
		var url = jQuery(inputHandle).val();
		this.checkClip(url);
	}

	this.checkClip = function(url) {
		var reqUrl = this.checkClipUrl;

		// show loadingel, hide detailel
		this.detailElement.css('display','none');
		this.loadingElement.css('display','block');

		jQuery.post( reqUrl, { "clipUrl": url }, jQuery.proxy(this.handleCheckClipResponse,this), "json" );
	}

	this.base64_decode = function(input) {
		var keyStr = "ABCDEFGHIJKLMNOP" +
			           "QRSTUVWXYZabcdef" +
			           "ghijklmnopqrstuv" +
			           "wxyz0123456789+/" +
			           "=";
		var output = "";
		var chr1, chr2, chr3 = "";
		var enc1, enc2, enc3, enc4 = "";
		var i = 0;

		// remove all characters that are not A-Z, a-z, 0-9, +, /, or =
		var base64test = /[^A-Za-z0-9\+\/\=]/g;
		if (base64test.exec(input)) {
			alert("There were invalid base64 characters in the input text.\n" +
			      "Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
			      "Expect errors in decoding.");
		}
		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

		do {
		enc1 = keyStr.indexOf(input.charAt(i++));
		enc2 = keyStr.indexOf(input.charAt(i++));
		enc3 = keyStr.indexOf(input.charAt(i++));
		enc4 = keyStr.indexOf(input.charAt(i++));

		chr1 = (enc1 << 2) | (enc2 >> 4);
		chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
		chr3 = ((enc3 & 3) << 6) | enc4;

		output = output + String.fromCharCode(chr1);

		if (enc3 != 64) {
		   output = output + String.fromCharCode(chr2);
		}
		if (enc4 != 64) {
		   output = output + String.fromCharCode(chr3);
		}

		chr1 = chr2 = chr3 = "";
		enc1 = enc2 = enc3 = enc4 = "";

		} while (i < input.length);

		return unescape(output);
	}
}