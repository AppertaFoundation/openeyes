var currentImage = 0;

/**
 * Changes the 
 */
function ev_mousemove (ev, id, imageObj, images, idOtherSide, imageObjOtherSide,
    imagesOtherSide, id_linked, imagesLinked) {
  var canvas = document.getElementById(id);
  var context = canvas.getContext("2d");
  var canvasOtherSide = document.getElementById(idOtherSide);
  var contextOtherSide = canvasOtherSide.getContext("2d");
  
  // Get the mouse position relative to the canvas element.
  if (ev.layerX || ev.layerX == 0) { // Firefox
    x = ev.layerX;
  } else if (ev.offsetX || ev.offsetX == 0) { // Opera
    x = ev.offsetX;
  }

  // image_width / images
  var incr = 300 / images.length;
  // image_width / increment
  
        var rect = canvas.getBoundingClientRect();
  x = ev.clientX - rect.left;
  var pos = x / incr;
//  alert(x + ', ' + ev.clientX + ', ' + rect.left);
//  alert(x + ', ' + incr  + ', ' + pos + ', ' +  images.length + ', ' +  imagesLinked.length + ', ' + currentImage);
  if (currentImage != Math.floor(pos)) {
    currentImage = Math.floor(pos);
    if (currentImage > images.length) {
      currentImage = images.length-1;
    }
//        alert(currentImage);
    imageObj.onload = function() {
      context.drawImage(imageObj, 0, 0);
    }
    imageObjOtherSide.onload = function() {
      contextOtherSide.drawImage(imageObjOtherSide, 0, 0);
    }
//    imageObjAlg.onload = function() {
//    }
    if (currentImage < images.length) {
      imageObj.src = images[currentImage];
      imageObjOtherSide.src = imagesOtherSide[currentImage];
    } else {
      return;
    }
        
    var canvasLinked = document.getElementById(id_linked);
        
    if (canvasLinked != null) {
      var context2 = canvasLinked.getContext("2d");
      var multiplier = 1;
      if (images.length < imagesLinked.length) {
        multiplier = Math.round(imagesLinked.length / images.length);
      } else if (images.length > imagesLinked.length) {
        multiplier = Math.round(images.length / imagesLinked.length);
      } else {
        multiplier = 1;
      }
      var imageObj2 = new Image();
      imageObj2.onload = function() {
        context2.drawImage(imageObj2, 0, 0);
        contextOtherSide.drawImage(imageObjOtherSide, 0, 0);
      }
      if (images.length < imagesLinked.length) {
        var index = 0;
        if (currentImage > 0) {
          index = Math.round((currentImage+1) * multiplier );
          if (index > imagesLinked.length -1) {
            index = imagesLinked.length -1;
          }
        }
        imageObj2.src = imagesLinked[index];
        imageObjOtherSide.src = imagesOtherSide[index];
      } else {
        var index = Math.floor( (currentImage + 1 ) / multiplier);
        imageObj2.src = imagesLinked[index];
        imageObjOtherSide.src = imagesOtherSide[index];
      }
    }
  }
}