


function togglePhoto(val)
{
   document.getElementById('sec1').style.display = 'none';
   document.getElementById('sec2').style.display = 'none';
   document.getElementById('sec3').style.display = 'none';


   document.getElementById('selectpho1').className = 'regular';
   document.getElementById('selectpho2').className = 'regular';
   document.getElementById('selectpho3').className = 'regular';

   

  // document.getElementById('area'+val).style.display = '';
  // document.getElementById('select'+val).className = 'selectnew';   
  
   $('#sec'+val).fadeIn(800);
   document.getElementById('selectpho'+val).className = 'selectpho';
   
   
}


 function toggleImages(val)
{
	 
   document.getElementById('div1').style.display = 'none';
   document.getElementById('div2').style.display = 'none';
   document.getElementById('div3').style.display = 'none';
   document.getElementById('div4').style.display = 'none';
   document.getElementById('div5').style.display = 'none';
   document.getElementById('div6').style.display = 'none';
   document.getElementById('div7').style.display = 'none';


   document.getElementById('link1').className = 'normal';
   document.getElementById('link2').className = 'normal';
   document.getElementById('link3').className = 'normal';
   document.getElementById('link4').className = 'normal';
   document.getElementById('link5').className = 'normal';
   document.getElementById('link6').className = 'normal';
   document.getElementById('link7').className = 'normal';


   //document.getElementById('div'+val).style.display = '';
   //document.getElementById('link'+val).className = 'selected';    
   
    $('#div'+val).fadeIn(800);
   document.getElementById('link'+val).className = 'selected';

}


function getval(val)
{
	 var res = val.substring(4,5); 
   document.getElementById('div1').style.display = 'none';
   document.getElementById('div2').style.display = 'none';
   document.getElementById('div3').style.display = 'none';
   document.getElementById('div4').style.display = 'none';
   document.getElementById('div5').style.display = 'none';
   document.getElementById('div6').style.display = 'none';
   document.getElementById('div7').style.display = 'none';


   document.getElementById('link1').className = 'normal';
   document.getElementById('link2').className = 'normal';
   document.getElementById('link3').className = 'normal';
   document.getElementById('link4').className = 'normal';
   document.getElementById('link5').className = 'normal';
   document.getElementById('link6').className = 'normal';
   document.getElementById('link7').className = 'normal';


   //document.getElementById('div'+val).style.display = '';
   //document.getElementById('link'+val).className = 'selected';    
   
    $('#div'+res).fadeIn(800);
   document.getElementById('link'+res).className = 'selected';
}




