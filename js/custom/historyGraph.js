/**
 * 
 */
function drawHistoryGraph(){
//console.log(historyArray);

var margin2 = {top: 20, right: 20, bottom: 50, left: 40},
	width2 = 900 - margin2.left - margin2.right,
	height2 = 502 - margin2.top - margin2.bottom;

var parseDate = d3.time.format("%d.%m.%Y").parse;

var x2 = d3.time.scale()
	.range([0, width2])
	.domain(d3.extent(historyArray,function(d){return parseDate(d.date);}))
	.nice(0.5 * d3.time.year);

var y2 = d3.scale.linear()
	.range([height2,0])
	.domain([(d3.min(historyArray, function(d){return +d.elo;}))-20,(d3.max(historyArray, function(d){return +d.elo;}))+20]);

var xAxis2 = d3.svg.axis()
	.scale(x2)
	.orient("bottom");

var yAxis2 = d3.svg.axis()
	.scale(y2)
	.orient("left")
	.tickSize(-width2);

var line = d3.svg.line()
	.x(function(d){return x2(parseDate(d.date));})
	.y(function(d){return y2(d.elo);});

var line0 = d3.svg.line()
.x(function(d){return x2(parseDate(d.date));})
.y(function(d){return height2;});

var graph = d3.select("#history").append("svg")
	.attr("id", "graph")
	.append("g").attr("transform", "translate(" + margin2.left + "," + margin2.top + ")");

	graph.append("g")
		.attr("class", "x axis")
		.attr("transform", "translate(0,"+height2+")")
		.call(xAxis2)
		.append("text")
			.attr("class","axislable")
			.attr("y",40)
			.attr("x",width2-20)
			.attr("dy",".71")
			.style("text-anchor","end")
			.text("date");

	graph.append("g")
		.attr("class", "y axis")
		.call(yAxis2)
		.append("text")
			.attr("class","axislable")
			.attr("transform", "rotate(-90)")
			.attr("y",-28)
			.attr("x",-20)
			.attr("dy",".71")
			.style("text-anchor","end")
			.text("elo points");
	
	graph.append("path")
		.datum(historyArray)
		.attr("class","line")
		.attr("d", line0)
		.transition()
		.ease(d3.ease("elastic",1.2,0.5))
		.delay(2000)
		.duration(2000)
		.attr("d",line);
	graph.selectAll(".eventPoint")
		.data(historyArray)
		.enter()
		.append("g").attr("class","eventPoint")
			.append("circle")
			.on("mouseover", function(d){d3.select(this).attr("r",5);
					graph.append("text")
						.attr("class","hoverInfo")
						.attr("x",d3.min([x2(parseDate(d.date))-30,width2-105]))
						.attr("y",y2(d.elo)+32)
						.text(d.elo+"@"+d.date);})
			.on("mouseleave", function(d){d3.select(this).attr("r",2);
					graph.select(".hoverInfo").remove();})
			.transition()
			.ease(d3.ease("elastic",1.2,0.5))
			.delay(2000)
			.duration(2000)
			.attr("cx",function(d){return x2(parseDate(d.date));})
			.attrTween("cy",function(d){return d3.interpolate(height2,(y2(d.elo)));})
			.attr("r", 2);
}