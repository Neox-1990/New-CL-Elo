$( document ).ready(function() {
	drawHistogramm();
	drawHistoryGraph();
});

function activateHisto(){
	d3.select("#chart").remove();
	drawHistogramm();
}

function activateDonut(){
	d3.select("#chart").remove();
	drawDonut();
}