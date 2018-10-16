<script>
	function addEmployeeCommas(for_what)
	{
		//alert(toCurrency(document.getElementById(for_what).value));
		document.getElementById(for_what).value = toEmployee(document.getElementById(for_what).value);
	}
	
	function toEmployee(num) {
		  var sign;
		  var cents;
		  var i;
		
		  num = num.toString().replace(/\$|\,/g, '');
		  if (isNaN(num)) {
			num = "0";
		  }
		  sign = (num == (num = Math.abs(num)));
		  num = Math.floor(num * 100 + 0.50000000001);
		  cents = num % 100;
		  num = Math.floor(num / 100).toString();
		  if (cents < 10) {
			cents = '0' + cents;
		  }
		
		  for (i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++) {
			num = num.substring(0, num.length - (4 * i + 3)) + ',' + num.substring(num.length - (4 * i + 3));
		  }
		
		  return (((sign) ? '' : '-') + '' + num);
		}
		

  </script>