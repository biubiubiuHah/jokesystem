<?php
function html($text)
{
	return htmlspecialchars($text, ENT_QUOTES,'UTF-8');
}
function htmlout($text)
{
	echo html($text);
}

/*function sum(n, fx) {
	sum = 0;
	for (itn i = 0;i < n;i++, sum += fx(i))
		;
	return sum;
}

function square(x) {
	return x * x;
}

sum = sum(10, square);


sum = sum(10, function (x) {
	return x * x * x;
})
int sum(int i)
{
	int i,sum1;
	for(i=0;i<10;i++)
	{
		sum1+=i*i*i;
	}
	return sum;
}

function sum($n,$fx)
{
	$sum = 0;
	for($i = 0;i < n;sum += fx(i))
		;
	return sum;
}

function square($x)
{
   return x*x;
}

$num = sum(10,square);

echo $num;*/