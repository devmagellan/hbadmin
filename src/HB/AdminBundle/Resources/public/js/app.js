(function( root, $, undefined ) {
  'use strict';

    /**
   * JS Implementation of MurmurHash3 (r136) (as of May 20, 2011)
   *
   * @author <a href="mailto:gary.court@gmail.com">Gary Court</a>
   * @see http://github.com/garycourt/murmurhash-js
   * @author <a href="mailto:aappleby@gmail.com">Austin Appleby</a>
   * @see http://sites.google.com/site/murmurhash/
   *
   * @param {string} key ASCII only
   * @param {number} seed Positive integer only
   * @return {number} 32-bit positive integer hash
   */

  function murmurhash3_32_gc(key, seed) {
  	var remainder, bytes, h1, h1b, c1, c1b, c2, c2b, k1, i;

  	remainder = key.length & 3; // key.length % 4
  	bytes = key.length - remainder;
  	h1 = seed;
  	c1 = 0xcc9e2d51;
  	c2 = 0x1b873593;
  	i = 0;

  	while (i < bytes) {
  	  	k1 =
  	  	  ((key.charCodeAt(i) & 0xff)) |
  	  	  ((key.charCodeAt(++i) & 0xff) << 8) |
  	  	  ((key.charCodeAt(++i) & 0xff) << 16) |
  	  	  ((key.charCodeAt(++i) & 0xff) << 24);
  		++i;

  		k1 = ((((k1 & 0xffff) * c1) + ((((k1 >>> 16) * c1) & 0xffff) << 16))) & 0xffffffff;
  		k1 = (k1 << 15) | (k1 >>> 17);
  		k1 = ((((k1 & 0xffff) * c2) + ((((k1 >>> 16) * c2) & 0xffff) << 16))) & 0xffffffff;

  		h1 ^= k1;
          h1 = (h1 << 13) | (h1 >>> 19);
  		h1b = ((((h1 & 0xffff) * 5) + ((((h1 >>> 16) * 5) & 0xffff) << 16))) & 0xffffffff;
  		h1 = (((h1b & 0xffff) + 0x6b64) + ((((h1b >>> 16) + 0xe654) & 0xffff) << 16));
  	}

  	k1 = 0;

  	switch (remainder) {
  		case 3: k1 ^= (key.charCodeAt(i + 2) & 0xff) << 16;
  		case 2: k1 ^= (key.charCodeAt(i + 1) & 0xff) << 8;
  		case 1: k1 ^= (key.charCodeAt(i) & 0xff);

  		k1 = (((k1 & 0xffff) * c1) + ((((k1 >>> 16) * c1) & 0xffff) << 16)) & 0xffffffff;
  		k1 = (k1 << 15) | (k1 >>> 17);
  		k1 = (((k1 & 0xffff) * c2) + ((((k1 >>> 16) * c2) & 0xffff) << 16)) & 0xffffffff;
  		h1 ^= k1;
  	}

  	h1 ^= key.length;

  	h1 ^= h1 >>> 16;
  	h1 = (((h1 & 0xffff) * 0x85ebca6b) + ((((h1 >>> 16) * 0x85ebca6b) & 0xffff) << 16)) & 0xffffffff;
  	h1 ^= h1 >>> 13;
  	h1 = ((((h1 & 0xffff) * 0xc2b2ae35) + ((((h1 >>> 16) * 0xc2b2ae35) & 0xffff) << 16))) & 0xffffffff;
  	h1 ^= h1 >>> 16;

  	return h1 >>> 0;
  }

  /*****
  * CONFIGURATION
  */
      //Main navigation
      $.navigation = $('nav > ul.nav');

  	$.panelIconOpened = 'icon-arrow-up';
  	$.panelIconClosed = 'icon-arrow-down';

  	//Default colours
  	$.brandPrimary =  '#20a8d8';
  	$.brandSuccess =  '#4dbd74';
  	$.brandInfo =     '#63c2de';
  	$.brandWarning =  '#f8cb00';
  	$.brandDanger =   '#f86c6b';

  	$.grayDark =      '#2a2c36';
  	$.gray =          '#55595c';
  	$.grayLight =     '#818a91';
  	$.grayLighter =   '#d1d4d7';
  	$.grayLightest =  '#f8f9fa';



  /****
  * MAIN NAVIGATION
  */

  $(document).ready(function($){

    // Add class .active to current link
    $.navigation.find('a').each(function(){

      var cUrl = String(window.location).split('?')[0];

      if (cUrl.substr(cUrl.length - 1) == '#') {
        cUrl = cUrl.slice(0,-1);
      }

      if ($($(this))[0].href==cUrl) {
        $(this).addClass('active');

        $(this).parents('ul').add(this).each(function(){
          $(this).parent().addClass('open');
        });
      }
    });

    // Dropdown Menu
    $.navigation.on('click', 'a', function(e){

      if ($.ajaxLoad) {
        e.preventDefault();
      }

      if ($(this).hasClass('nav-dropdown-toggle')) {
        $(this).parent().toggleClass('open');
        resizeBroadcast();
      }

    });

    function resizeBroadcast() {

      var timesRun = 0;
      var interval = setInterval(function(){
        timesRun += 1;
        if(timesRun === 5){
          clearInterval(interval);
        }
        if (navigator.userAgent.indexOf('MSIE') !== -1 || navigator.appVersion.indexOf('Trident/') > 0) {
          var evt = document.createEvent('UIEvents');
          evt.initUIEvent('resize', true, false, window, 0);
          window.dispatchEvent(evt);
        } else {
        window.dispatchEvent(new Event('resize'));
        }
      }, 62.5);
    }

    /* ---------- Main Menu Open/Close, Min/Full ---------- */
    $('.sidebar-toggler').click(function(){
      $('body').toggleClass('sidebar-hidden');
      resizeBroadcast();
    });

    $('.sidebar-minimizer').click(function(){
      $('body').toggleClass('sidebar-minimized');
      resizeBroadcast();
    });

    $('.brand-minimizer').click(function(){
      $('body').toggleClass('brand-minimized');
    });

    $('.aside-menu-toggler').click(function(){
      $('body').toggleClass('aside-menu-hidden');
      resizeBroadcast();
    });

    $('.mobile-sidebar-toggler').click(function(){
      $('body').toggleClass('sidebar-mobile-show');
      resizeBroadcast();
    });

    $('.sidebar-close').click(function(){
      $('body').toggleClass('sidebar-opened').parent().toggleClass('sidebar-opened');
    });

    /* ---------- Disable moving to top ---------- */
    $('a[href="#"][data-top!=true]').click(function(e){
      e.preventDefault();
    });

  });

  /****
  * CARDS ACTIONS
  */

  $(document).on('click', '.card-actions a', function(e){
    e.preventDefault();

    if ($(this).hasClass('btn-close')) {
      $(this).parent().parent().parent().fadeOut();
    } else if ($(this).hasClass('btn-minimize')) {
      var $target = $(this).parent().parent().next('.card-body');
      if (!$(this).hasClass('collapsed')) {
        $('i',$(this)).removeClass($.panelIconOpened).addClass($.panelIconClosed);
      } else {
        $('i',$(this)).removeClass($.panelIconClosed).addClass($.panelIconOpened);
      }

    } else if ($(this).hasClass('btn-setting')) {
      $('#myModal').modal('show');
    }

  });

  function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  function init(url) {

    /* ---------- Tooltip ---------- */
    $('[rel="tooltip"],[data-rel="tooltip"]').tooltip({"placement":"bottom",delay: { show: 400, hide: 200 }});

    /* ---------- Popover ---------- */
    $('[rel="popover"],[data-rel="popover"],[data-toggle="popover"]').popover();

  }

  // Production steps of ECMA-262, Edition 6, 22.1.2.1
  if (!Array.from) {
    Array.from = (function () {
      var toStr = Object.prototype.toString;
      var isCallable = function (fn) {
        return typeof fn === 'function' || toStr.call(fn) === '[object Function]';
      };
      var toInteger = function (value) {
        var number = Number(value);
        if (isNaN(number)) { return 0; }
        if (number === 0 || !isFinite(number)) { return number; }
        return (number > 0 ? 1 : -1) * Math.floor(Math.abs(number));
      };
      var maxSafeInteger = Math.pow(2, 53) - 1;
      var toLength = function (value) {
        var len = toInteger(value);
        return Math.min(Math.max(len, 0), maxSafeInteger);
      };

      // The length property of the from method is 1.
      return function from(arrayLike/*, mapFn, thisArg */) {
        // 1. Let C be the this value.
        var C = this;

        // 2. Let items be ToObject(arrayLike).
        var items = Object(arrayLike);

        // 3. ReturnIfAbrupt(items).
        if (arrayLike == null) {
          throw new TypeError('Array.from requires an array-like object - not null or undefined');
        }

        // 4. If mapfn is undefined, then let mapping be false.
        var mapFn = arguments.length > 1 ? arguments[1] : void undefined;
        var T;
        if (typeof mapFn !== 'undefined') {
          // 5. else
          // 5. a If IsCallable(mapfn) is false, throw a TypeError exception.
          if (!isCallable(mapFn)) {
            throw new TypeError('Array.from: when provided, the second argument must be a function');
          }

          // 5. b. If thisArg was supplied, let T be thisArg; else let T be undefined.
          if (arguments.length > 2) {
            T = arguments[2];
          }
        }

        // 10. Let lenValue be Get(items, "length").
        // 11. Let len be ToLength(lenValue).
        var len = toLength(items.length);

        // 13. If IsConstructor(C) is true, then
        // 13. a. Let A be the result of calling the [[Construct]] internal method
        // of C with an argument list containing the single item len.
        // 14. a. Else, Let A be ArrayCreate(len).
        var A = isCallable(C) ? Object(new C(len)) : new Array(len);

        // 16. Let k be 0.
        var k = 0;
        // 17. Repeat, while k < len… (also steps a - h)
        var kValue;
        while (k < len) {
          kValue = items[k];
          if (mapFn) {
            A[k] = typeof T === 'undefined' ? mapFn(kValue, k) : mapFn.call(T, kValue, k);
          } else {
            A[k] = kValue;
          }
          k += 1;
        }
        // 18. Let putStatus be Put(A, "length", len, true).
        A.length = len;
        // 20. Return A.
        return A;
      };
    }());
  }

  // https://tc39.github.io/ecma262/#sec-array.prototype.includes
  if (!Array.prototype.includes) {
    Object.defineProperty(Array.prototype, 'includes', {
      value: function(searchElement, fromIndex) {

        if (this == null) {
          throw new TypeError('"this" is null or not defined');
        }

        // 1. Let O be ? ToObject(this value).
        var o = Object(this);

        // 2. Let len be ? ToLength(? Get(O, "length")).
        var len = o.length >>> 0;

        // 3. If len is 0, return false.
        if (len === 0) {
          return false;
        }

        // 4. Let n be ? ToInteger(fromIndex).
        //    (If fromIndex is undefined, this step produces the value 0.)
        var n = fromIndex | 0;

        // 5. If n ≥ 0, then
        //  a. Let k be n.
        // 6. Else n < 0,
        //  a. Let k be len + n.
        //  b. If k < 0, let k be 0.
        var k = Math.max(n >= 0 ? n : len - Math.abs(n), 0);

        function sameValueZero(x, y) {
          return x === y || (typeof x === 'number' && typeof y === 'number' && isNaN(x) && isNaN(y));
        }

        // 7. Repeat, while k < len
        while (k < len) {
          // a. Let elementK be the result of ? Get(O, ! ToString(k)).
          // b. If SameValueZero(searchElement, elementK) is true, return true.
          if (sameValueZero(o[k], searchElement)) {
            return true;
          }
          // c. Increase k by 1.
          k++;
        }

        // 8. Return false
        return false;
      }
    });
  }

  var segmentApiKey = (typeof hbApp!='undefined' && typeof hbApp.segmentKey!='undefined')?hbApp.segmentKey:'8RZQWgwC95FfphdUwXmFnWDN3bxg41CQ',
    segmConfig={key:segmentApiKey},
    segmentLaunch= function(key){
  		analytics.load(key);

  		// if (typeof analytics!=='undefined') {
  		// 	var eventsArr=['identify', 'track', 'page']
  		// 	for (var index = 0; index < eventsArr.length; index++) {
  		// 		var evtData = {
  		// 			name: eventsArr[index],
  		// 			event: '',
  		// 			properties: '',
  		// 			options: '',
  		// 			user: '',
  		// 		}
  		//
  		// 		analytics.on(eventsArr[index], function(event, properties, options){
  		// 			evtData.event=event
  		// 			evtData.properties=properties
  		// 			evtData.options=options
  		// 			if (typeof analytics.user=='function') {
  		// 				evtData.user=analytics.user()
  		// 			}
  		// 		})
  		//
  		// 		console.log(evtData);
  		// 	}
  		// }

  		analytics.page();
  	},
  	initSegment = function(config) {
      var analytics=window.analytics=window.analytics||[];
      if(!analytics.initialize)
      if(analytics.invoked){
        segmentLaunch(config.key);
      } else {
        analytics.invoked=!0;
        analytics.methods=["trackSubmit","trackClick","trackLink","trackForm","pageview","identify","reset","group","track","ready","alias","debug","page","once","off","on"];
        analytics.factory=function(t){
          return function(){
            var e=Array.prototype.slice.call(arguments);
            e.unshift(t);
            analytics.push(e);
            return analytics;
          };
        };
        for(var t=0;t<analytics.methods.length;t++){
          var e=analytics.methods[t];analytics[e]=analytics.factory(e);
        }
        analytics.load=function(t,e){
          loadscripts([{id:'segment-js', src:"https://cdn.segment.com/analytics.js/v1/"+t+"/analytics.min.js"}]);
          analytics._loadOptions=e;
        };
      analytics.SNIPPET_VERSION="4.1.0";
      segmentLaunch(config.key);
    }
  	},
  	viewport= function(){
  		var e = window,
  		a = 'inner';
  		if ( !( 'innerWidth' in window ) )
  		{
  			a = 'client';
  			e = document.documentElement || document.body;
  		}
  		return { width : e[ a+'Width' ] , height : e[ a+'Height' ] }
  	},
  	vp=viewport(),
  	scriptsLoaded=[],
  	scriptCb=function(params){
  		if (typeof params=='undefined') {
  			return console.log('No params provided for scriptCb');
  		}
  		var callback=params.el.cb,
  			cbparams=(typeof params.el.cbparams!=='undefined')?params.el.cbparams:null;
  		if (typeof params.script!=='undefined') {
  			if (params.script.readyState) { // IE, incl. IE9
  				params.script.onreadystatechange = function() {
  					if (params.script.readyState == "loaded" || params.script.readyState == "complete") {
  						params.script.onreadystatechange = null;
  						if (typeof callback!=='undefined') callback(cbparams);
  						return scriptsLoaded.push(params.el.id);
  					}
  				};
  			} else {
  				params.script.onload = function(){
  					if (typeof callback!=='undefined') callback(cbparams);
  					return scriptsLoaded.push(params.el.id);
  				};
  			}
  		} else {
  			var scriptIsLoaded=(scriptsLoaded.indexOf(params.el.id) > -1);
  			if(typeof params.el.cb!=='undefined'&&params.el.cb !== null&&scriptIsLoaded){
  				if (typeof callback!=='undefined') callback(cbparams);
  			} else {
  				setTimeout(function(){scriptCb(params)}, 500);
  			}
  		}
  	},
  		// usage: loadscripts([{id: String elid, src: String elsrc, cb: function func, cbparams: Object params}]);
  	// checks if script is loaded allready by id and writes back to array scriptsLoaded
  	loadscripts=function (arr){
  		var params, i;
  		for (i = 0; i < arr.length; i++) {
  			if (document.getElementById(arr[i].id)==null&&scriptsLoaded.indexOf(arr[i].id) < 0) {
  				var s=arr[i],
  					script = document.createElement('script');
  				script.id=s.id;
  				script.src = s.src;
  				scriptCb({el:s, script:script});
  				document.getElementsByTagName('head')[0].appendChild(script);
  			} else {
  				scriptCb({el:arr[i]});
  			}
  		}
  	},
  		// usage: loadstyles([{id: String elid, src: String elsrc}]);
  	loadstyles=function(arr){
  		for (var i = 0; i < arr.length; i++) {
  			if (document.getElementById('#'+arr[i].id)==null) {
  				var head = document.getElementsByTagName('head')[0],
  					lnk = document.createElement('link'),
  					func = function () {
  						head.appendChild(lnk);
  					};
  				lnk.rel = 'stylesheet';
  				lnk.id = arr[i].id;
  				lnk.href = arr[i].src;

  				var img = document.createElement('img');
  				img.onerror = function(){
  					func();
  				}
  				img.src = arr[i].src;
  			}
  		}
  	},
  	_dcq = _dcq || [],
  	_dcs = _dcs || {},
  	userId;

  window.addEventListener('load', function(event){
  	initSegment(segmConfig);

  	_dcs.account=hbApp.dripAccount;
  	loadscripts([{id:'drip-js', src:'//tag.getdrip.com/'+_dcs.account+'.js'}]);

  	if (typeof analytics!=='undefined') {
  		var userId=hbApp.userEmail,
  			integrationData={
  				integrations: {
  					Intercom : {
  						user_hash : hbApp.userHash
  					}
  				}
  			},
  			userData={
  				name: hbApp.userName,
  				email: hbApp.userEmail,
  				// createdAt: Date.now()
  			};

  		analytics.identify(userId, userData, integrationData);
    }
  });


	document.addEventListener('DOMContentLoaded', function(){
		window.dataLayer=window.dataLayer||[]
		if(hbApp!=='undefined'&&hbApp.userEmail!=='undefined') {
			var dlConf={
				'userId': murmurhash3_32_gc(hbApp.userEmail, 8),
			}

			dataLayer.push(dlConf)
		}
	})

  document.addEventListener('click', function(event){
	if(event.target.nodeName=='A'&&event.target.href.search(/https:\/\/app\.monstercampaigns\.com/g)!= -1){
		event.preventDefault()
		if (typeof om_loaded=='undefined') {
			var link=document.createElement('a')
			link.href=event.target.href
			link.style.position='absolute'
			link.style.left='-9999px'
			link.innerHTML='OptinMonster link'
			link.target='_blank'

			document.body.appendChild(link)

			link.click()
		}
	}
})
} ( this, jQuery ));
