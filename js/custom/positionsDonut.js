/**
 * 
 */
function drawDonut(){
	positions[0]=0;
	positions2=new Array(0, positions[1], positions[2], positions[3], 0);
	for(var i=4;i<=40;i++){
		positions2[4]+=positions[i];
	}
	//console.log(positions);
	//console.log(positions2);
	var margin3 = {top: 0, right: 35, bottom: 0, left: 35},
		width3 = 500 - margin3.left - margin3.right,
		height3 = 200 - margin3.top - margin3.bottom;
	var radius = 95;
	var center1 = {x:100,y:100}
		center2 = {x:315,y:100};
	
	var arc0 = d3.svg.arc()
		.outerRadius(0)
		.innerRadius(0);
	
	var pie0 = d3.layout.pie()
		.sort(null)
		.value(function(d) { return 0; });
	
	var arc1 = d3.svg.arc()
    	.outerRadius(radius)
    	.innerRadius(radius * 0.5);

	var pie1 = d3.layout.pie()
    	.sort(null)
    	.value(function(d) { return d; });
	
	var arc2 = d3.svg.arc()
		.outerRadius(radius)
		.innerRadius(radius * 0.5);
	
	var pie2 = d3.layout.pie()
		.sort(null)
		.value(function(d) { return d; });
	
	var donutSVG = d3.select("#positions").append("svg")
		.attr("id", "chart")
	    .attr("width", width3+margin3.left+margin3.right)
	    .attr("height", height3).append("g")
	    	.attr("transform", "translate("+margin3.left+",0)");
	
	var donut1text1 = donutSVG.append("text")
		.attr("class","donutInnerText")
		.style("text-anchor","middle")
		.attr("x", center1.x)
		.attr("y", center1.y-25)
		.attr("dy","+0.3em")
		.text("");
	var donut1text2 = donutSVG.append("text")
		.attr("class","donutInnerText")
		.style("text-anchor","middle")
		.attr("x", center1.x)
		.attr("y", center1.y)
		.attr("dy","+0.3em")
		.text("");
	var donut1text3 = donutSVG.append("text")
		.attr("class","donutInnerText")
		.style("text-anchor","middle")
		.attr("x", center1.x)
		.attr("y", center1.y+25)
		.attr("dy","+0.3em")
		.text("");
	
	var donut2text1 = donutSVG.append("text")
		.attr("class","donutInnerText")
		.style("text-anchor","middle")
		.attr("x", center2.x)
		.attr("y", center2.y-25)
		.attr("dy","+0.3em")
		.text("");
	var donut2text2 = donutSVG.append("text")
		.attr("class","donutInnerText")
		.style("text-anchor","middle")
		.attr("x", center2.x)
		.attr("y", center2.y)
		.attr("dy","+0.3em")
		.text("");
	var donut2text3 = donutSVG.append("text")
		.attr("class","donutInnerText")
		.style("text-anchor","middle")
		.attr("x", center2.x)
		.attr("y", center2.y+25)
		.attr("dy","+0.3em")
		.text("");
	
	var donut1 = donutSVG.append("g")
		.attr("transform", "translate(" + center1.x + "," + center1.y + ")");
	var donut2 = donutSVG.append("g")
		.attr("transform", "translate(" + center2.x + "," + center2.y + ")");
	
	var g1 = donut1.selectAll(".arc")
		.data(pie1(positions))
	    .enter().append("g")
	    .attr("class", "arc");
	
	g1.append("path")
	    .attr("d", arc1)
		.style("opacity",0.0)
		.on("mouseover",function(d,i){
			donut1text1.text(d.data+" x");
			donut1text2.text("finished on");
			donut1text3.text("P "+i);
		})
		.on("mouseleave",function(d,i){
			donut1text1.text("");
			donut1text2.text("");
			donut1text3.text("");
		})
		.transition()
		.ease(d3.ease("sin"))
		.delay(500)
		.duration(1500)
		.style("opacity",1.0);
	
	var g2 = donut2.selectAll(".arc")
	    .data(pie2(positions2))
	    .enter().append("g")
	    .attr("class", "arc");
	
	g2.append("path")
	    .attr("d", arc2)
		.style("fill",function(d,i){
			if(i==1) return "#ffc600";
			if(i==2) return "#AAA";
			if(i==3) return "#c85000";
			else return "#4B4";
		})
		.style("opacity",0.0)
		.on("mouseover",function(d,i){
			donut2text1.text(d.data+" x");
			donut2text2.text("finished on");
			if(i!=4)donut2text3.text("P "+i);
			else donut2text3.text("P>3");
		})
		.on("mouseleave",function(d,i){
			donut2text1.text("");
			donut2text2.text("");
			donut2text3.text("");
		})
		.transition()
		.ease(d3.ease("sin"))
		.delay(500)
		.duration(1500)
		.style("opacity",1.0);
	

}