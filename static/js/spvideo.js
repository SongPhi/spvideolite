var SPVideoClass = function(_baseUrl, _loadingEl, _detailElement) {
	this.baseUrl = _baseUrl;
	this.loadingElement = _loadingEl;
	this.detailElement = _detailElement;
	this.checkClipUrl = this.baseUrl + '/ajax_get_clip';

	

	this.checkClipFromElement = function(inputHandle) {
		var url = jQuery(inputHandle).val();
		this.checkClip(url);
	}

	this.checkClip = function(url) {
		var reqUrl = this.checkClipUrl;

		// show loadingel, hide detailel
		this.detailElement.css('display','none');
		this.loadingElement.css('display','block');

		jQuery.post( reqUrl, { "clipUrl": url }, jQuery.proxy(this.handleCheckClipResponse,this), "json" )
			.fail(jQuery.proxy(this.handleCheckClipError,this));
	}

	this.handleCheckClipResponse = function(data) {
		//TODO: code here
		console.log(data);
		
		// hide loadingel, show detailel
		this.detailElement.css('display','block');
		this.loadingElement.css('display','none');

		// display response
		this.detailElement.html( '' );
		if (!data.error) {
			this.detailElement.html( Base64.decode(data.formHtml) );
		}
	}

	this.handleCheckClipError = function(data) {
		//TODO: code here
	}
}