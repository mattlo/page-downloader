/**
 * Application Namespace
 * @type {Object}
 */
var App = {};

App.Constants = (function () {
	'use strict';
	
	return {
		/**
		 * @type {Number}
		 */
		SECTION_ANIMATION_DURATION: 700,
		/**
		 * @type {String}
		 */
		SECTIONS_CSS_SELECTOR: '.input-form > section'
	};
}());

App.Enums = (function () {
	'use strict';
	
	return {
		SUCCESS: 0,
		PROCESSING: 1,
		FAILED: 2,
		QUEUED: 3
	};
}());

App.Url = (function () {
	'use strict';
	
	var WARNING_CODES = [
		{clsName: 'success', label: 'Downloaded'},
		{clsName: 'primary', label: 'Processing...'},
		{clsName: 'danger', label: 'Failed'},
		{clsName: 'default', label: 'Queued'}
	];
	
	function Url(url, status, index) {
		var fakeNode = $('<a />')[0];
		fakeNode.href = url;
		
		this.url = fakeNode;
		this.status = status;
		this.index = index;
	}
	
	Url.prototype = {
		generateStatus: function(status) {
			this.status = status;
			
			var path = this.url.pathname;
			
			if (App.Global.Config.saveAsHtmlExt === true) {
				path = path.replace(/\.[^/.]+$/, '.html');
			}
			
			return '<a href="' + App.Global.Config.assetPrefix + path + '" target="_blank" class="url">' + App.Global.Config.assetPrefix + this.url.pathname + '</a> ' +
				'<span class="label label-' + WARNING_CODES[this.status].clsName + '">' +WARNING_CODES[this.status].label + '</span>';
		}
	};
	
	return Url;
}());

App.Request = (function () {
	'use strict';
	
	return function () {
		var url = App.Global.Config.urlList.shift();
		
		$('.console > div').eq(url.index).html(url.generateStatus(App.Enums.PROCESSING));
		
		// @TODO refactor request. also use `data` prop. (disable URI encode on url but the rest is is fine)
		$.ajax({
			url: '/src/app?url=' + url.url + '&srcroot=' + App.Global.Config.assetPrefix + (App.Global.Config.saveAsHtmlExt === true ? '&htmlext=true' : '')
		}).success(function() {
			$('.console > div').eq(url.index).html(url.generateStatus(App.Enums.SUCCESS));
		}).fail(function () {
			$('.console > div').eq(url.index).html(url.generateStatus(App.Enums.FAILED));
		}).done(function () {
			if (App.Global.Config.urlList.length > 0) {
				App.Request();
			}
		});
	}
}());

App.Global = {
	Config: {
		/**
		 * @type {String}
		 */
		assetPrefix: '/output',
		/**
		 * @type {Array}
		 */
		urlList: [],
		/**
		 * @type {Boolean}
		 */
		saveAsHtmlExt: false
	}
};

// Main Process
(function () {
	'use strict';

	$(document).ready(function () {
		// bind all step sections
		var sections = $(App.Constants.SECTIONS_CSS_SELECTOR),
			consoleOut = $('.console'),
			i;
		
		// show first one
		sections.eq(0).slideDown(App.Constants.SECTION_ANIMATION_DURATION);
		
		$('.review-btn').click(function () {
			var urls = $('.input-url-list textarea').val().split('\n'),
				assetPrefix = $('.input-asset-prefix input').val(),
				saveAsHtmlExt = $('.input-html-output input').prop('checked');
				
			// iterate over lines
			for (i = 0; i < urls.length; ++i) {
				App.Global.Config.urlList.push(new App.Url(urls[i], App.Enums.QUEUED, i));
			}
			
			// save options
			App.Global.Config.assetPrefix = assetPrefix;
			App.Global.Config.saveAsHtmlExt = saveAsHtmlExt;
			
			// update review display
			$('.confirm-url-count .form-control').text(App.Global.Config.urlList.length);
			$('.confirm-asset-prefix .form-control').text(App.Global.Config.assetPrefix);
			$('.confirm-output-html .form-control').text(App.Global.Config.saveAsHtmlExt === true ? 'Yes' : 'No');
			
			// continue to review
			sections.eq(0).slideUp(App.Constants.SECTION_ANIMATION_DURATION);
			sections.eq(1).slideDown(App.Constants.SECTION_ANIMATION_DURATION);
		});
		
		// final confirm button trigger
		$('.run-btn').click(function () {
			// hide button
			$('.input-group.pull-right, h2', sections.eq(1)).hide();
			
			// update console
			for (i = 0; i < App.Global.Config.urlList.length; ++i) {
				console.log(App.Global.Config.urlList[i]);
				consoleOut.append('<div>' + App.Global.Config.urlList[i].generateStatus(App.Enums.QUEUED) + '</div>');
			}
			
			App.Request();
		});
	});
}());

