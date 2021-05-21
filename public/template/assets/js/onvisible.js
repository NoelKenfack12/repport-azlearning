/*
Copyright (C) 2016 de Zentsoft Developpez
*/

/*
 * @version 0.1
 */
 
 var pointVisible = function(point) {
	if (point.left > jQuery(document).scrollLeft()
		&& point.left < jQuery(document).scrollLeft() + jQuery(window).width()
		&& point.top > jQuery(document).scrollTop()
		&& point.top < jQuery(document).scrollTop() + jQuery(window).height()
	) {
		return true;
	}
	return false;
}
	
function visibleElement(elem)
{
	var offsetNO = $(elem).offset();
	var offsetNE = {
		top: offsetNO.top,
		left: offsetNO.left + $(elem).width()
	};
	var offsetSO = {
		top: offsetNO.top + $(elem).height(),
		left: offsetNO.left
	};
	var offsetSE = {
		top: offsetNO.top + $(elem).height(),
		left: offsetNO.left + $(elem).width()
	};
	if (pointVisible(offsetNO) || pointVisible(offsetNE) || pointVisible(offsetSO) || pointVisible(offsetSE)) {
		return 1;
	}else{
		return 0;
	}
}
function fullyVisibleElement(elem)
{
	var offsetNO = $(elem).offset();
	var offsetNE = {
		top: offsetNO.top,
		left: offsetNO.left + $(elem).width()
	};
	var offsetSO = {
		top: offsetNO.top + $(elem).height(),
		left: offsetNO.left
	};
	var offsetSE = {
		top: offsetNO.top + $(elem).height(),
		left: offsetNO.left + $(elem).width()
	};
	if (pointVisible(offsetNO) && pointVisible(offsetNE) && pointVisible(offsetSO) && pointVisible(offsetSE)) {
		return 1;
	}else{
		return 0;
	}
}