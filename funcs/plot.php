<?php
class plot2D {
    var $img, $imgWidth, $imgHeight,
        $fontSize, $fontWidth, $fontHeight, $cBack, $acPlot, $maxCatLen, $aItems, $maxVal, $numItems,
        $maxNumItems, $maxLinesDesc, $numberY, $cBlack, $cGrid;
    
    var $graphX, $graphY, $graphWidth, $graphHeight;

    var $titleString, $titleFontSize, $cTitle, $titleWidth, $titleHeight;
    var $xDesc, $yDesc, $xDescLength, $yDescLength;

    function plot2D($width=650, $height=480, $fontSize=2, $red=0xFF, $green=0xFF, $blue=0xFF) {
        $this->img = imageCreate($width, $height);
        $this->imgWidth = $width-10;
        $this->imgHeight = $height;
        $this->cBack = imageColorAllocate($this->img, $red, $green, $blue);
        $this->cBlack = imageColorAllocate($this->img, 0, 0, 0);
        $this->cNavy = imageColorAllocate($this->img, 0x00, 0x00, 0x80);
        $this->fontSize = $fontSize;
        $this->fontWidth = imageFontWidth($fontSize);
        $this->fontHeight = imageFontHeight($fontSize);
        $this->graphX = 0;
        $this->graphY = 0;
        $this->graphWidth = $this->imgWidth;
        $this->graphHeight = $this->imgHeight;
        $this->setGrid();
    }
 
    function setTitle($title, $fontSize=3, $red=0x00, $green=0x00, $blue=0x00) {
        $this->titleString = $title;
        $this->titleFontSize = $fontSize;
        $this->cTitle = imageColorAllocate($this->img, $red, $green, $blue);
        $this->titleWidth = imageFontWidth($fontSize) * strLen($title);
        $this->titleHeight = imageFontHeight($fontSize);
        $this->graphY = $this->graphY + ($this->titleHeight + 2);
        $this->graphHeight = $this->graphHeight - ($this->titleHeight + 2);
    }
 
    function setDescription($x, $y="") {
        if ($x) {
            $this->xDesc = $x;
            $this->xDescLength = $this->fontWidth * strLen($x);
            $this->graphHeight = $this->graphHeight - ($this->fontHeight + 2);
        }
  
        if ($y) {
            $this->yDesc = $y;
            $this->yDescLength = $this->fontWidth * strLen($y);
            $this->graphX = $this->graphX + ($this->fontHeight + 2);
            $this->graphWidth = $this->graphWidth - ($this->fontHeight + 2);
        }
    }
 
    function setGrid($number=5, $red=0xCC, $green=0xCC, $blue=0xCC) { //Horizontal grid
        $this->cGrid = imageColorAllocate($this->img, $red, $green, $blue);
        $this->numberY = $number;
    }
 
    function addCategory($description, $red, $green, $blue) {
        $this->acPlot[$description] = imageColorAllocate($this->img, $red, $green, $blue);
        if (strLen($description) > $this->maxCatLen)
            $this->maxCatLen = (strLen($description)+5);
    }
 
    function categoryExists($description) {
        if (isSet($this->acPlot[$description])) {
            return true;
        } else {
            return false;
        }
    }
 
    function addItem($category, $description, $value) {
        $this->aItems[$category][$description] = $value;
        $this->checkItem($category, $description, $this->aItems[$category][$description]);
    }
 
    function incrementItem($category, $description, $value) {
        if (!isSet($this->aItems[$category][$description]))
            $this->addItem($category, $description, $value);
        $this->aItems[$category][$description] = $this->aItems[$category][$description] + $value;
        $this->checkItem($category, $description, $this->aItems[$category][$description]);
    }
 
    function checkItem($category, $description, $value) {
        if ($value > $this->maxVal)
            $this->maxVal = $value;
        $this->numItems[$category]++;
        if ($this->numItems[$category] > $this->maxNumItems)
            $this->maxNumItems = $this->numItems[$category];
        if (sizeOf(explode("\n", $description)) > $this->maxLinesDesc)
            $this->maxLinesDesc = sizeOf(explode("\n", $description));
    }
 
    function destroy() {
        imageDestroy($this->img);
    }
 
    function printGraph($filename = 0) {
        $this->graphHeight = $this->graphHeight - (($this->maxLinesDesc + 1) * ($this->fontHeight + 2)); //Adjust for bottom values
        $this->graphX = $this->graphX + ($this->fontWidth * strLen($this->maxVal) + 2); //Adjust left margin for values
        $this->graphWidth = $this->graphWidth - ($this->fontWidth * strLen($this->maxVal) + 2) - (($this->maxCatLen * $this->fontWidth) + 17) - (strLen($this->maxVal) * $this->fontWidth); //+17 = Adjust right margin for category descriptions
        imageRectangle($this->img, $this->graphX, $this->graphY, $this->graphX + $this->graphWidth, $this->graphY + $this->graphHeight, $this->cBlack); //Box
        if ($this->titleString)
            imageString($this->img, $this->titleFontSize, $this->graphX + ($this->graphWidth / 2) - ($this->titleWidth / 2), 0, $this->titleString, $this->cTitle);
        if ($this->xDesc)
            imageString($this->img, $this->fontSize, $this->graphX + ($this->graphWidth / 2) - ($this->xDescLength / 2), $this->graphY + $this->graphHeight + (($this->maxLinesDesc + 1) * ($this->fontHeight + 2)), $this->xDesc, $this->cBlack);
        if ($this->yDesc)
            imageStringUp($this->img, $this->fontSize, 0, ($this->graphY + ($this->graphHeight / 2)) + ($this->xDescLength / 2), $this->yDesc, $this->cBlack);
  
        for ($i = 0; $i <= $this->numberY; $i++) { //Grid
            $yPos = $this->graphY + $i * ($this->graphHeight / ($this->numberY + 1));
            if ($i)
                imageLine($this->img, $this->graphX+1, $yPos, $this->graphX + $this->graphWidth - 1, $yPos, $this->cGrid);
            $yVal = floor($this->maxVal - (($this->maxVal / ($this->numberY + 1)) * $i) + .5);
            imageString($this->img, $this->fontSize, $this->graphX - 3 - ($this->fontWidth * strLen($yVal)), $yPos - ($this->fontHeight / 2), $yVal, $this->cNavy);
        }
  
        imageString($this->img, $this->fontSize, $this->graphX - 3 - $this->fontWidth, $this->graphY + $this->graphHeight - ($this->fontHeight / 2), "0", $this->cNavy);
  
        $c = 0;
        while (list($cDesc, $cColor) = each($this->acPlot)) { //Loop through categories
            $s=0;
            while (list($sKey, $sVal) = each($this->aItems[$cDesc])) { //Loop through items
                $sum[$cDesc] += $sVal; //Sum up values for each category
                $xPos = ($this->graphX + 4) + ((($this->graphWidth - 10) / ($this->maxNumItems - 1)) * $s);
                $yPos = $this->graphY + ($this->graphHeight - floor((($sVal / $this->maxVal) * $this->graphHeight) + .5));
                imageRectangle($this->img, $xPos - 2, $yPos - 2, $xPos + 2, $yPos + 2, $cColor);
                if ($s)
                    imageLine($this->img, $prevX, $prevY, $xPos, $yPos, $cColor);

                $aValues = explode("\n", $sKey);
                for ($i = 0; $i < sizeOf($aValues); $i++) {
                    imageString($this->img, $this->fontSize, $xPos - ((strLen($aValues[$i]) * $this->fontWidth) / 2), $this->graphY + $this->graphHeight + 2 + ($i * ($this->fontHeight + 2)), $aValues[$i], $this->cNavy); //Bottom values
                }
                $prevX = $xPos;
                $prevY = $yPos;
                $s++;
            }
            $boxX = $this->graphX + $this->graphWidth + 5;
            $boxY = $this->graphY + 10 + ($c * 15);
            imageFilledRectangle($this->img, $boxX, $boxY, $boxX + 10, $boxY + 10, $cColor);
            imageRectangle($this->img, $boxX, $boxY, $boxX + 10, $boxY + 10, $this->cBlack);
            imageString($this->img, $this->fontSize, $boxX + 14, ($boxY + 5) - ($this->fontHeight / 2), $cDesc."=".$sum[$cDesc], $cColor);
            $c++;
        }
        if ($filename) {
            imagePNG($this->img, "$filename.png");
        } else {
            imagePNG($this->img);
        }
    }
}

?>
