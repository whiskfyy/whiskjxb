var map = new BMap.Map( map_id );

if(height < 300) {
	var opts = {type: BMAP_NAVIGATION_CONTROL_SMALL};
} else {
	var opts = null;
}
map.addControl(new BMap.NavigationControl(opts)); 

if(p1 != '' && p2 != '') {
	var c = new BMap.Point(p1, p2); 
    map.centerAndZoom(c, view_level);
	if(show > 0) {
		var marker = new BMap.Marker(c);
		var lbl = new BMap.Label(title);
		lbl.setStyle({color:"red", fontSize:"12px"});
		marker.setLabel(lbl);
		map.addOverlay(marker); 
	}
} else {
	map.centerAndZoom(new BMap.Point('120.1519','30.2448'), view_level);
}

function markmap() {
	map.clearOverlays();
	var myPushpin = new BMap.PushpinTool(map);
	myPushpin.addEventListener("markend", function(e){
		document.getElementById('point1').value = e.marker.getPoint().lng;
		document.getElementById('point2').value = e.marker.getPoint().lat;
	});
	myPushpin.open();
}