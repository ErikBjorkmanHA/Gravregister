var maxZoom = 1;
var minZoom = 0.3;



window.onload = function() {
  const elements = document.getElementsByClassName('grave');

  const graveyard = document.getElementById("graveyard");

  const info = document.getElementById("gravinfo");

  /*Om man vill veta hur många gravplatser som finns */
  var count = 0;
  var startScale = 0.3;
  for (const element of elements) {
      element.addEventListener("click", event => {
        info.style.display  = "block";
        var graveId = transformId(event.target.id);
        document.getElementById("gravadress").innerHTML = graveId;

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState === 4 && this.status === 200) {
            document.getElementById("inner_display").innerHTML = this.responseText;
          }
        };
        xmlhttp.open("GET", "data.php?graveId=" + graveId, true);
        xmlhttp.send();

      })

      count++;
  }

  console.log(count);

  var move = false;
  var offset = [0,0];
  var mousePosition;

  graveyard.addEventListener('mousedown', function(event) {
    move = true;
    graveyard.style.cursor = "grabbing";
    offset = [
      graveyard.offsetLeft - event.clientX,
      graveyard.offsetTop - event.clientY
  ];
  }, true);

  window.addEventListener('mouseup', function() {
    move = false;
    graveyard.style.cursor = "default";
  }, true);

  document.addEventListener('mousemove', function(event) {
    event.preventDefault();
    if (move) {
          mousePosition = {
            x : event.clientX,
            y : event.clientY
        };


        //Räknar ut vart man ska flytta i x- och y-led
        var horizontal = mousePosition.x + offset[0];
        var vertical = mousePosition.y + offset[1];


        var mapSize = 0.5; //så här stor andel av fönstret måste alltid täckas upp av kartan

        //Begränsar användaren att flytta kartan utanför bild
        if(horizontal <= window.innerWidth*mapSize && (horizontal + (graveyard.offsetWidth)*getScale()[0]) > (window.innerWidth*mapSize)){
          graveyard.style.left = (horizontal) + 'px';
        }

        if(vertical <= window.innerHeight*mapSize && (vertical + (graveyard.offsetHeight)*getScale()[1]) > (window.innerHeight*mapSize)){
          graveyard.style.top  = (vertical) + 'px';
        }

    }

  }, true);
}


//Ett html element kan inte ha en siffra som första tecken i sitt id.
//Därför är det skrivet i romerska siffror istället. Den här översätter en gravs id till numeriska siffror.
function transformId(id){ 

  var transformedId = id;

  if(transformedId.includes("III")){
    transformedId = transformedId.replace("III", "3");
  } else if (transformedId.includes("II")){
    transformedId = transformedId.replace("II", "2");
  } else if (transformedId.includes("I")){
    transformedId = transformedId.replace("I", "1");
  }

  return transformedId;
}

function roundToHalf(number) {
  return Math.round(number*10)/10;
}






function zoom(event){

  //Hämtar ut nuvarande skalningsvärden
  var scaleX = getScale()[0];
  var scaleY = getScale()[1];

  //Hur mycket som zoomas per rullning på musens hjul
  var zoomSize = 0.1;

  if(event.deltaY < 0){ //zoom in
    if(scaleX <maxZoom && scaleY < maxZoom){

      graveyard.style.transform = "scale("+(scaleX+zoomSize),(scaleY+zoomSize) +")";

    }
  } else { //zoom ut
    if(scaleX > minZoom && scaleY > minZoom){

      graveyard.style.transform = "scale("+(scaleX-zoomSize),(scaleY-zoomSize) +")";

      

      var rect = graveyard.getBoundingClientRect(); //hämtar koordinaterna på gravgårdens fyra sidor

      if(rect.left< 0){
        graveyard.style.left = (rect.left + (window.innerWidth*0.33)) +'px';
      }

      if(rect.top<0){
        graveyard.style.top = (rect.top + (window.innerHeight*0.33)) +'px';
      }

      
      
    }
    
  }
  //återställer origin till top left
  graveyard.style.transformOrigin = "top left";
}

function getScale(){

  var matrix = window.getComputedStyle(graveyard).transform;
  var matrixArray = matrix.replace("matrix(", "").split(",");
  var scaleX = roundToHalf(parseFloat(matrixArray[0])); //floats är inte exakta, så använder en
  var scaleY = roundToHalf(parseFloat(matrixArray[3])); //funktion för att avrunda till närmaste .1
  console.log(scaleX + "," + scaleY);

  console.log("X: " + scaleX + "  Y:"+scaleY);
  return [scaleX,scaleY];
}

function closeWindow(){
  document.getElementById("gravinfo").style.display = "none";
}
