

function switchDisplay(idElement)
{
      var displayMode = $("div#"+idElement).css("display");
      var newDisplay = "";
      if (displayMode == "block")
	  newDisplay = "none";
      else
	  newDisplay = "block";

      $("div#"+idElement).css("display", newDisplay);
}