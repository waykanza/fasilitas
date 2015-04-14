<?php
function ex_equal($p, $v, $m = '')
{
	if ($p == $v)
	{
		throw new Exception($m);
	}
}

function ex_empty($v, $m = '')
{
	if ($v == '')
	{
		throw new Exception($m);
	}
}

function ex_zero($v, $m = '')
{
	if ($v == '0' || $v === 0 || $v < 1)
	{
		throw new Exception($m);
	}
}

function ex_less_then($v, $l, $m)
{
	if ($v < $l)
	{
		throw new Exception($m);
	}
}

function ex_false($v, $m = '')
{
	if ( ! $v)
	{
		throw new Exception($m);
	}
}

function ex_found($v, $m = '')
{
	if ($v > 0)
	{
		throw new Exception($m);
	}
}

function ex_notfound($v, $m = '')
{
	if ($v < 1)
	{
		throw new Exception($m);
	}
}