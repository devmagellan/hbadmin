(function() {

  // проверяем поддержку
  if (!Element.prototype.closest) {

    // реализуем
    Element.prototype.closest = function(css) {
      var node = this;

      while (node) {
        if (node.matches(css)) return node;
        else node = node.parentElement;
      }
      return null;
    };
  }

})();

window.addEventListener('load', function(){

	var closest= function(num, arr) {
			var curr = arr[0],
				diff = num - curr;
			for (var val = 0; val < arr.length; val++) {
				var newdiff = num - arr[val];
				if (newdiff >= 0) {
					diff = newdiff;
					curr = arr[val];
				}
			}
			return curr;
		},
		priceSlider = function(params){
			var sliders = document.querySelectorAll(params.selector),
				ranges=[],
				values=[],
				priceParams={
					'subscribers':{
						0: {count: 100, month: 0, year: 0},
						1: {count: 2000, month: 39, year: 390},
						2: {count: 5000, month: 79, year: 790},
						3: {count: 15000, month: 149, year: 1490},
						4: {count: 30000, month: 249, year: 2490},
						5: {count: 50000, month: 399, year: 3990},
						6: {count: '50000+' ,month: 599, year: 3990},
						// 100000: {count: 100000 ,month: 899, year: 5990},
						// 200000: {count: 200000 ,month: 1199, year: 8990},
						// 300000: {count: 300000 ,month: 1499, year: 11990},
						// 400000: {count: 400000 ,month: 1799, year: 14990},
						// 500000: {count: 500000 ,month: 1799, year: 16990},
					},
					'roomSize':{
						0: {count: 0, month: 0, year: 0},
						1: {count: 100, month: 10, year: 100},
						2: {count: 250, month: 25, year: 250},
						3: {count: 500, month: 50, year: 500},
						4: {count: 1000, month: 100, year: 1000},
					},
					'funnels':{
						0: {count: 0,month: 0, year: 0},
						1: {count: 1,month: 10, year: 100},
						2: {count: 2,month: 20, year: 200},
						3: {count: 3,month: 30, year: 300},
						4: {count: 4,month: 40, year: 400},
						5: {count: 5,month: 50, year: 500},
						6: {count: 6,month: 60, year: 600},
						7: {count: 7,month: 70, year: 700},
						8: {count: 8,month: 80, year: 800},
						9: {count: 9,month: 90, year: 900},
						10: {count: 10,month: 100, year: 1000},
						11: {count: 11,month: 110, year: 1100},
						12: {count: 12,month: 120, year: 1200},
						13: {count: 13,month: 130, year: 1300},
						14: {count: 14,month: 140, year: 1400},
					},
          currData: {
            price: 0,
            subscribers: 0,
            roomSize: 0,
            funnels: 0,
          }
				},
				calcPrice=function(){
          var total={
  						// yearPrice: priceParams.subscribers.year + priceParams.roomSize.year,
  						monthPrice: calcParams.subscribers.month + calcParams.roomSize.month + calcParams.funnels.month + Math.ceil(parseInt(calcParams.subscribers.count) / 1000) * calcParams.funnels.count,
  					},
            html=(total.monthPrice<500)?'<span>'+total.monthPrice+'</span>$ в месяц':'По запросу'
            // formula=calcParams.subscribers.month +' + '+ calcParams.roomSize.month +' + '+ calcParams.funnels.month +' + '+ Math.ceil(parseInt(calcParams.subscribers.count) / 1000) +' * '+ calcParams.funnels.count +' = '+ total.monthPrice

          // document.querySelector('.js-priceConsole').innerText=formula
          priceParams.currPrice=total.monthPrice

					document.querySelector(params.targetPriceMonth).innerHTML=html
					// document.querySelector(params.targetPriceYear).innerHTML=total.yearPrice
				},
				calcParams={subscribers:priceParams.subscribers[0], roomSize:priceParams.roomSize[0], funnels: priceParams.funnels[0]};
				calcPrice(calcParams)

      document.getElementById('unlimPrice').parentNode.querySelector('.js-addVip').addEventListener('change', function(event){
        var checked=event.target.checked,
          numElem= document.getElementById('unlimPrice')

        if (checked) {
          numElem.innerText='По запросу'
        } else if(priceParams.currPrice<500) {
          numElem.innerHTML='<span>'+priceParams.currPrice+'</span>$ в месяц'
        }

      })

			for (var i = 0; i < sliders.length; i++) {
				ranges.push(sliders[i].querySelector('.range-slider__range'))
				values.push(sliders[i].querySelector('.range-slider__value'))

				ranges[i].dataset.index=i
				values[i].innerHTML=ranges[i].value

				ranges[i].addEventListener('input', function(event){
					// var keys=Object.keys(priceParams[event.target.dataset.type]),
					var currVal=event.target.value,
						// closestVal=closest(currVal, keys),
						type=event.target.dataset.type;
					calcParams[type]=priceParams[type][currVal];

          priceParams.currData[type]=currVal

          calcPrice()

					values[event.target.dataset.index].innerHTML=calcParams[type].count
				})
			}
		};

	priceSlider({selector: '.js-range-slider', targetPriceMonth: '#unlimPrice', targetPriceYear: '#unlimPriceYear'});

})
