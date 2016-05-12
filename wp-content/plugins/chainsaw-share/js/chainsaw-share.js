var inkwellShare;

;(function($){

	$(window).load(function(){
		inkwellShare = new InkwellShare();
	});

	function InkwellShare(){
		this.bindHandlers();
	}

	InkwellShare.prototype.bindHandlers = function(){
		$('a[data-service="email"]').on('click', this.onShareMailClick);
	};

	InkwellShare.prototype.onShareMailClick = function(e){
		var pid = $('#pid').val();
		if (pid){
			inkwellShare.registerShare(pid, 'email');
		}
	};

	InkwellShare.prototype.registerShare = function(pid, service){
		var data = {pid:pid, service:service, action:'count_share'};
		$.post(ajaxurl, data, function(response){
			console.log(response);
		});
	};

})(jQuery);

var inkwellShareFBLike;

;(function($){

	$(document).ready(function(){
		inkwellShareFBLike = new InkwellShareFBLike();
	});

	function InkwellShareFBLike(){
		this.loadScript();
		this.hideEmptys();
		this.bindHandlers();
	}

	InkwellShareFBLike.prototype.hideEmptys = function(){
		$('.share-count').each(function(){
			var $sc = $(this);
			if (!$sc.text() || $sc.text() == '0'){
				$sc.hide();
			}
		});
	}

	InkwellShareFBLike.prototype.bindHandlers = function(){
		$('[href="#like"]').on('click', this.onLike);
	};

	InkwellShareFBLike.prototype.registerLike = function(){
		console.log('registerLike');
		var $a = $('[href="#like"]');
		var $shareCounter = $a.parent().find('.share-count');
		console.log($shareCounter);
		var shareCount = Number($shareCounter.text());
		shareCount++;
		$shareCounter.text(shareCount);
	};

	InkwellShareFBLike.prototype.prepLikeCount = function(){
		var $a = $('[href="#like"]');
		var $shareCounter = $a.parent().find('.share-count');
		$shareCounter.fadeIn();
		var shareCount = Number($shareCounter.text());
		if (!shareCount){
			$shareCounter.text(1);
		}
	};

	InkwellShareFBLike.prototype.onLike = function(){
		FB.getLoginStatus(function(response) {
			if (response.status == 'connected'){
				var uid = response.authResponse.userID;
				var token = response.authResponse.accessToken;
				inkwellShareFBLike.like(uid, token);
			} else {
				FB.login(inkwellShareFBLike.onLike, {scope:'publish_actions, publish_stream'});
			}
		});
		return false;
	};

	InkwellShareFBLike.prototype.like = function(uid, token){
		var data = {'access_token':token, 'object':window.location.href};
		FB.api('me/og.likes', 'post', data, function(resp){
			console.log(resp);
			if (resp.error){
				inkwellShareFBLike.prepLikeCount();
				if (resp.error.code != 3501){
					inkwellShareFBLike.registerLike();
				}
			}
		});
	};

	InkwellShareFBLike.prototype.setLoginStatus = function(){

	};

	InkwellShareFBLike.prototype.onScriptLoad = function(){
		window.fbAsyncInit = function() {
			FB.init({
				appId: $('meta[name="facebook_app_id"]').attr('content'),
				channelUrl: '//yourapp.com/channel.html'
			});
			$('#loginbutton,#feedbutton').removeAttr('disabled');
			FB.getLoginStatus(inkwellShareFBLike.setLoginStatus);
		};
	};

	InkwellShareFBLike.prototype.loadScript = function(){
		var THIS = this;
		$.ajaxSetup({ cache: true });
		$.getScript('//connect.facebook.net/en_UK/all.js', THIS.onScriptLoad);
	};

})(jQuery);
