<?php
header('Content-Type:text/html;charset=utf-8 ');
session_start();
require_once '../../util/Settings_INI.php';
$settings = new Settings_INI();
$settings->load($_SERVER['DOCUMENT_ROOT'] . "/chinapay_demo/config/path.properties");
$pay_url = $settings->get("pay_url");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="../css/style.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Insert title here</title>
</head>
<body>
	<form name="payment" action="<?php echo $pay_url ?>" method="POST"
		target="_blank">
		<table border="1" cellpadding="2" cellspacing="0"
			style="border-collapse: collapse" bordercolor="#111111">
			<tr>
				<td width="200px"><font color=red>*</font>商户号</td>

				<td width="800px">
	            <?php echo $_SESSION['MerId']?>
	        </td>
			</tr>
			<tr>
				<td><font color=red>*</font>订单号</td>

				<td>
			     <?php echo $_SESSION['MerOrderNo']?>
	        </td>
			</tr>
			<tr>
				<td><font color=red>*</font>订单金额</td>

				<td>
			     <?php echo $_SESSION['OrderAmt']?>
	        </td>
			</tr>
			<tr>
				<td><font color=red>*</font>交易日期</td>

				<td>
			     <?php echo $_SESSION['TranDate']?>
	        </td>
			</tr>
			<tr>
				<td><font color=red>*</font>交易时间</td>

				<td>
			     <?php echo $_SESSION['TranTime']?>
	        </td>
			</tr>
			<tr>
				<td>交易类型</td>

				<td>
			     <?php echo $_SESSION['TranType']?>
	        </td>
			</tr>

			<tr>
				<td><font color=red>*</font>业务类型</td>

				<td>
			     <?php echo $_SESSION['BusiType']?>
	         </td>
			</tr>
			<tr>
				<td><font color=red>*</font>版本号</td>

				<td>
			     <?php echo $_SESSION['Version']?>
	           </td>
			</tr>
			<tr>
				<td><font color=red></font>支付超时时间</td>

				<td>
			     <?php echo $_SESSION['PayTimeOut']?>
	           </td>
			</tr>

			<tr>
				<td>分账类型</td>

				<td>
			     <?php echo $_SESSION['SplitType']?>
	           </td>
			</tr>
			<tr>
				<td>分账方式</td>

				<td>
			     <?php echo $_SESSION['SplitMethod']?>
	           </td>
			</tr>
			<tr>
				<td>分账公式</td>

				<td>
			     <?php echo $_SESSION['MerSplitMsg']?>
	           </td>
			</tr>
			<tr>
				<td><font color=red></font>支付机构号</td>

				<td>
			     <?php echo $_SESSION['BankInstNo']?>
	           </td>
			</tr>

			<tr>
				<td><font color=red></font>商户系统时间戳</td>

				<td>
			     <?php echo $_SESSION['TimeStamp']?>
	           </td>
			</tr>
			<tr>
				<td><font color=red></font>商户客户端IP</td>

				<td>
			     <?php echo $_SESSION['RemoteAddr']?>
	           </td>
			</tr>

			<tr>
				<td><font color=red></font>交易币种</td>

				<td>
			     <?php echo $_SESSION['CurryNo']?>
	           </td>
			</tr>

			<tr>
				<td>接入类型</td>

				<td>
			     <?php echo $_SESSION['AccessType']?>
	           </td>
			</tr>
			<tr>
				<td><font color=red></font>收单机构号</td>

				<td>
			     <?php echo $_SESSION['AcqCode']?>
	           </td>
			</tr>
			<tr>
				<td><font color=red></font>商品信息描述</td>

				<td>
			     <?php echo $_SESSION['CommodityMsg']?>
	           </td>
			</tr>

			<tr>
				<td>页面应答接收URL</td>

				<td>
			     <?php echo $_SESSION['MerPageUrl']?>
	           </td>
			</tr>
			<tr>
				<td><font color=red></font>后台应答接收URL</td>

				<td>
			     <?php echo $_SESSION['MerBgUrl']?>
	              </td>
			</tr>
			<tr>
				<td>商户私有域</td>

				<td>
			     <?php echo $_SESSION['MerResv']?>
	        </td>
			</tr>
			<tr>
				<td>交易扩展域</td>

				<td>
			     <?php echo $_SESSION['TranReserved']?>
	        </td>
			</tr>
			<tr>
				<td>有卡交易信息域</td>

				<td>
			     <?php echo $_SESSION['CardTranData']?>
	        </td>
			</tr>
			<tr>
				<td>签名信息</td>

				<td>
			     <?php echo $_SESSION['Signature']?>
	        </td>
			</tr>
		</table>
		<hr>
		<?php
$params = "TranReserved;MerId;MerOrderNo;OrderAmt;CurryNo;TranDate;SplitMethod;BusiType;MerPageUrl;MerBgUrl;SplitType;MerSplitMsg;PayTimeOut;MerResv;Version;BankInstNo;CommodityMsg;Signature;AccessType;AcqCode;OrderExpiryTime;TranType;RemoteAddr;Referred;TranTime;TimeStamp;CardTranData";
foreach ($_SESSION as $k => $v) {
    if (strstr($params, $k)) {
        echo "<input type='hidden' name = '" . $k . "' value ='" . $v . "'/>";
    }
}
?>
		如果您的浏览器没有弹出支付页面，请点击按钮<input type='button' value='提交订单'
			onClick='document.payment.submit()'>再次提交。
	</form>
	<script language=JavaScript>
	document.payment.submit();
</script>
</body>
</html>
<?php
session_destroy();
?>