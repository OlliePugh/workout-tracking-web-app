var currentImageIndex = 0;
var speed = 3000; //time in milliseconds that each image is displayed

var slides = document.getElementById("slides").getElementsByTagName('img');

for (var i = 0; i < (slides.length-1); i++) { //make only the first image visible
  slides[i+1].style.display = "none";
}

function updateIndex(direction){
  if (direction > 0 || direction === undefined){
    if ((currentImageIndex+1) == slides.length){ //if has reached end of array
      currentImageIndex = 0; //set index to start of the array
    }
    else{
      currentImageIndex++;
    }
  }
  else if (direction < 0) {
    if ((currentImageIndex - 1) < 0){
      currentImageIndex = (slides.length - 1) //set index to the end of the array
    }
    else{
      currentImageIndex--;
    }
  }
  else{ //direction == 0
    console.log("Invalid direction");
  }
}

function changeImage(direction){
  slides[currentImageIndex].style.display = "none";
  updateIndex();
  slides[currentImageIndex].style.display = "block";
}

setInterval(changeImage, speed);
