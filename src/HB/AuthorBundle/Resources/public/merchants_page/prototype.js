function toTime(sec,s){
	/*var mas = s||[],t = s%60;

	mas.push(t);
	console.log(mas);
	return Math.floor((3940 / ( 60 * 60)))+':'+*/;
	if(!sec) {return null}
	var t = {
		s : Math.floor((sec) % 60),
		m : Math.floor((sec / 60) % 60),
		h: Math.floor((sec / ( 60 * 60))),
	}
	if(t.h && t.m<10){
		//console.log("t.h,t.m,(t.m+'').length",t.h,t.m,(t.m+'').length);
		t.m='0'+t.m;
	}
	// console.log(t.s.length);
	if(t.s<10){
		t.s='0'+t.s;
	}
	t.s = t.s+'';
	t.m=t.m?t.m+':':"0:";
	t.h=t.h?t.h+':':'';
	// console.log(t);
	return  t.h+t.m+t.s;
}
$.prototype.toSec = function(){
	var time=0,
	m=this.html().replace(/\s/g, '').split(':');
	for (var i = 0; i < m.length-1; i++) {
		var ms = 1;
		time+=+(m[i]*Math.pow(60,(m.length-(i+1))))||0;

	}
	return time+=+m[m.length-1];
}

$.prototype.loop_slick = function(argument){
	$(this).each(function(index, el) {
		$(el).slick({
			nextArrow:'<button type="button" class="slick-next"><i class="icon-play"></i></button>',
			prevArrow:'<button type="button" class="slick-prev"><i class="icon-play"></i></button>',

		});


	});
};

$.prototype.ptimer = function(date,windmessage){
	// body...
	if(!$(this)[0]){
		return false
	}
	var timer = this;
	date = (timer[0].dataset?timer[0].dataset.dend:false) ||date||false;
	windmessage = windmessage || '<h1>Событие уже прошло</h1>';


	if(!date){
		console.error('DATE ERROR. set date mm/dd/yyyy for:',timer);
		timer.css('display','none');
		return false;
	}


	function getTimeRemaining(endtime) {
		var t = Date.parse(endtime) - Date.parse(new Date());
		var seconds = Math.floor((t / 1000) % 60);
		var minutes = Math.floor((t / 1000 / 60) % 60);
		var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
		var days = Math.floor(t / (1000 * 60 * 60 * 24));
		return {
			'total': t,
			'days': days,
			'hours': hours,
			'minutes': minutes,
			'seconds': seconds
		};
	}

	function initializeClock(id, endtime) {
		var clock = id;
		var daysSpan = clock.find('.days')[0]||false;
		var hoursSpan = clock.find('.hours')[0]||false;
		var minutesSpan = clock.find('.minutes')[0]||false;
		var secondsSpan = clock.find('.seconds')[0]||false;
		/*console.log(clock,daysSpan,hoursSpan,minutesSpan,secondsSpan);*/

		function updateClock() {
			var t = getTimeRemaining(endtime);


			$(daysSpan).hasClass('false')&&t.total<(1 * 24 * 60 * 60 * 1000)? $(daysSpan).parent().remove() : daysSpan.innerHTML = format(t.days);
			$(hoursSpan).hasClass('false')&&t.total<(1 * 60 * 60 * 1000)? $(hoursSpan).parent().remove() : hoursSpan.innerHTML = format(t.hours);
			$(minutesSpan).hasClass('false')&&t.total<(1 * 60 * 1000)? $(minutesSpan).parent().remove() : minutesSpan.innerHTML = format(t.minutes);
			$(secondsSpan).hasClass('false')&&t.total<(1 * 1000)? $(secondsSpan).parent().remove() : secondsSpan.innerHTML = format(t.seconds);
			//daysSpan.innerHTML = format(t.day);
			//hoursSpan.innerHTML = format(t.hours);
			//minutesSpan.innerHTML = format(t.minutes);
			//secondsSpan.innerHTML = format(t.seconds);
		}

		function format(t){


			return "<div>"+('0' + t).slice(-2)[0]+"</div>"+"<div>"+('0' + t).slice(-2)[1]+"</div>"

		}

		updateClock();
		var timeinterval = setInterval(updateClock, 1000);
	}

	var deadline = date ;//new Date(Date.parse(new Date()) + 15 * 24 * 60 * 60 * 1000);

	initializeClock(this, deadline);
};

$.prototype.dadd = function(atr){
	atr =atr || 'load'
	$(this).each(function(index, el) {
		$(this).css({
			'background-image': 'url("'+this.dataset.dadd+'")',
		});
		$(this).addClass('load');
	});
}

$.prototype.isrich = function(arg,t){
	/*if(t){
		console.log(arg+'>='+$(this).offset().top,arg>=$(this).offset().top)
	}*/
	if(!this[0] || !arg){
		return false;
	}
	if(arg>=$(this).offset().top ){
		return true;
	}
	return false;
}

$.prototype.mystar = function(uoption){
	if(!this[0]) {return false
	}
	option={
		raiting:Number(this[0].dataset.raiting) || 0,
		click:false
	}
	$.extend(option,uoption);
	$(this).each(function(index, el) {
		var r = 100/(5/option.raiting),
		t=$(el),
		hider = $(t).find('.star_hider');
		hider.width(r+'%');
		var timer;
		if(option.click){
			t.addClass('clicked');
			t.click(function(e) {
				/* Act on the event */
				var x = e.offsetX==undefined?e.layerX:e.offsetX;
				var nr = (x*100)/t.width();
				nr = nr>100?100:nr;
				r = nr.toFixed(2);
				hider.width(nr+'%');
				if($(this).find('.star-popup')[0]){
					popup = $(this).find('.star-popup');
				}
				else{
					$(this).append('<div class="star-popup"></div>').animate({opacity:1},500);
					popup = $(this).find('.star-popup');
				}
				popup.stop(true,false);
				popup.text((x/100*5).toFixed(1));
				if(timer) { clearTimeout(timer);}
				timer =setTimeout((function(){popup.animate({opacity: 0},300,function() {popup.remove();});}), 1000);
			});
		}
	});
};


$.prototype.mgoto = function(event) {
	var el =this[0].hash,
	t =1500;
	if(!el) {return false;}
	event.preventDefault();
	$('body,html').animate({
		scrollTop: ($(el).offset().top-$('.head').innerHeight())}, t);
};
$.prototype.rmax = function(){
	var max = 0
	$(this).each(function(index, el) {
		max = $(el).innerHeight() > max? $(el).innerHeight() : max;
	});
	return max;
}

/*$.prototype.lpack = function() {



	$(this).each(function(index, el) {
		var a= {
			tel : $(el),
			head : $(el).find('.lpack_head'),
			reflactopr :$(el).find('.lpack_head-reflactor');
		}
	});

	function lpackscrool(){
		if(top >= $(this).offset().top ){
			$(this).each(function(index, el) {

				var ttop=top- tel.offset().top;

				reflactopr.height(head.innerHeight());
				console.log(reflactopr,)
				head.css({
					top: ttop+'px',
					position: 'absolute'
				});
			});
		}
		var top = (document.documentElement.scrollTop || document.body.scrollTop)+$('header').innerHeight();

	}

};*/


;(function(factory) {
	'use strict';
	if (typeof define === 'function' && define.amd) {
		define(['jquery'], factory);
	} else if (typeof exports !== 'undefined') {
		module.exports = factory(require('jquery'));
	} else {
		factory(jQuery);
	}

}(function($) {
	'use strict';
	var Lpack = window.Lpack || {};
	var gheight = 0;

	Lpack = (function() {

		var instanceUid = 0;

		function Lpack(element, settings) {
			var _ = this,
			defsettings={
				synch:false
			};
			_.def = {
				tel : $(element),
				head : $(element).find('.lpack_head'),
				reflactopr :$(element).find('.lpack_head-reflactor'),
				settings: $.extend(defsettings, settings)
			}

			_.init(true);
		}
		return	Lpack;
	}());

	Lpack.prototype.init = function(){

		var _ = this;
		_.def.tel.css({"padding-top":_.def.head.innerHeight()});
		_.lpackcount();
		$(window).on('scroll', $.proxy(_.lpackscrl, _) );
		$(window).on('resize', $.proxy(_.lpackres, _) );
		_.def.tel.addClass('lpack_innit');
		/*
			if(_.def.settings.synch && $('.lpack_el')[$('.lpack_el').length-1] == _.def.tel[0]){
				$('.lpack_el').lpack('lpackres');
			}
			// Закоментировал, так как тут появляется ошибка, по непонятной причине, вроде на функциональность не влияет.
			//custom.js:357 Uncaught TypeError: Cannot read property 'lpackres' of undefined
		*/
	}

	Lpack.prototype.lpackscrl = function(event) {

		var  _ = this,
		top = (document.documentElement.scrollTop || document.body.scrollTop)+$('header').innerHeight();
		if(top >= _.def.offsettop ){
			var ttop=top- _.def.offsettop;
			_.def.head.css({
				top: ttop+'px',
			});
		}
		if(top <= _.def.offsettop ){
			_.def.head.css({
				top: '0px',
			});
		}
		if(top >( _.def.bottom )){
			_.def.head.css({
				top:  (_.def.telheight-_.def.headheight )+'px',
			});
		}
	};
	Lpack.prototype.lpackres = function(rec) {

		var _ = this;
		clearTimeout(_.windowDelay);
		_.windowDelay = window.setTimeout(function() {
			_.lpackcount();
		}, 60);
	}
	Lpack.prototype.lpackcount = function(rec) {

		var _ = this;

		if(_.def.settings.synch){
			$('.lpack_el').height('auto');
			$('.lpack_el').height($('.lpack_el').rmax());
			_.def.max = $('.lpack_el').rmax();
		} else {_.def.max = _.def.tel.innerHeight();}
		_.def.headheight = _.def.head.innerHeight();
		_.def.reflactopr.height(_.def.headheight);
		_.def.telheight=  _.def.max;
		_.def.offsettop = _.def.tel.offset().top;
		_.def.bottom = _.def.offsettop + _.def.telheight-_.def.headheight;
		_.lpackscrl();
		/*console.log(
			_.def.headheight,
			_.def.telheight,
			_.def.offsettop,
			_.def.bottom,
			)*/
	}
	Lpack.prototype.pack = function(){

		var _ = this;
		return _;
	}
	$.fn.lpack = function() {
		var _ = this,
		opt = arguments[0],
		args = Array.prototype.slice.call(arguments, 1),
		l = _.length,
		i,
		ret;
		for (i = 0; i < l; i++) {
			if (typeof opt == 'object' || typeof opt == 'undefined')
				_[i].lpack = new Lpack(_[i], opt);
			else
				ret = _[i].lpack[opt].apply(_[i].lpack, args);
			if (typeof ret != 'undefined') return ret;
		}
		return _;
	};

}));
