function drawHistogramm(){
positions[0]=0;
//console.log(positions);
var margin = {top: 10, right: 10, bottom: 20, left: 35},
	width = 500 - margin.left - margin.right,
	height = 200 - margin.top - margin.bottom;

var x = d3.scale.linear()
	.range([0, width])
	.domain([0.1,40]);

var y = d3.scale.linear()
	.range([height, 0])
	.domain([0, 1.1*d3.max(positions)]);

var xAxis = d3.svg.axis()
	.scale(x)
	.orient("bottom")
	.ticks(20,".");

var yAxis = d3.svg.axis()
	.scale(y)
	.orient("left")
	.ticks(d3.min([10,d3.max(positions)]))
	.tickSize(-width);

var chart = d3.select("#positions").append("svg")
	.attr("id", "chart")
	.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	chart.append("g")
		.attr("class", "x axis")
		.attr("transform", "translate(0," + height + ")")
		.call(xAxis)
		.append("text")
			.attr("class","axislable")
			.attr("x", -14)
			.attr("y", 16)
			.attr("dx", "0.71em")
			.style("text-anchor", "end")
			.text("pos");
	
	chart.append("g")
		.attr("class", "y axis")
		.call(yAxis)
		.append("text")
			.attr("class","axislable")
			.attr("transform", "rotate(-90)")
			.attr("x", -10)
			.attr("y", -20)
			.style("text-anchor", "end")
			.text("times");
	chart.selectAll(".posGroups")
		.data(positions)
		.enter()
		.append("g")
			.attr("class", "posGroups")
			.append("rect")
			.transition()
			.ease(d3.ease("bounce"))
			.delay(500)
			.duration(1500)
			.attr("class", "posBar")
			.attr("x", function (d,i){return x(i)-6;})
			.attr("width", 11)
			.attrTween("y", function(d){return d3.interpolate(height,y(d));})
			.attrTween("height", function(d){return d3.interpolate(0,height - y(d));});
	chart.selectAll(".posGroups")
		.append("text")
			.transition()
			.ease(d3.ease("bounce"))
			.delay(500)
			.duration(1500)
			.attr("class","barText")
			.attr("x", function(d,i){if(d<10)return x(i)-3;else return x(i)-5})
			.attrTween("y", function(d){return d3.interpolate(height,y(d)-2);})
			.text(function (d){if(d!=0)return d;else return""});	
}