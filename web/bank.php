<?php

//#1 报错： [ethjs-query] while formatting outputs from RPC '{"value":{"code":-32603,"data":{"code":-32000,"message":"rlp: expected input list for types.TxData"}}}'
//解决：换个浏览器就好了。微软Edge不行，换火狐后可以了。（oklink浏览器使用场景）这里也可以试下

function http_get($url, $aHeader = 1)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_TIMEOUT, 5000);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    // curl_setopt($curl,CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $aHeader);
    // if($type == 1){
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // }
    $res = curl_exec($curl);
    if ($res) {
        curl_close($curl);
        return $res;
    } else {
        $error = curl_errno($curl);
        curl_close($curl);
        return $error;
    }
}

function http_post($sUrl, $aHeader, $aData)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $sUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aData));
    $sResult = curl_exec($ch);
    if ($sError = curl_error($ch)) {
        die($sError);
    }
    curl_close($ch);
    return $sResult;
}
// ————————————————
// 版权声明：本文为CSDN博主「江南极客」的原创文章，遵循CC 4.0 BY-SA版权协议，转载请附上原文出处链接及本声明。
// 原文链接：https://blog.csdn.net/sinat_35861727/article/details/87184042
$url = 'https://www.oklink.com/api/v5/explorer/address/address-balance-fills';

https://api.etherscan.io/v2/api?chainid=11155111&module=account&action=addresstokenbalance&address=0x983e3660c0bE01991785F80f266A84B911ab59b0&page=1&offset=100&apikey=PZJEDUF4JTPHJP42UD2STHEK1YP8J25PK6

//@see https://docs.etherscan.io/api-reference/endpoint/addresstokenbalance?playground=open
//@see curl -X 'GET' \
  //'https://pro-openapi.debank.com/v1/user/chain_balance?id=0x5853ed4f26a3fcea565b3fbc698bb19cdf6deb85&chain_id=eth' \
  //-H 'accept: application/json' -H 'AccessKey: YOUR_ACCESSKEY'?

//https://sepolia-api.ethplorer.io/getAddressInfo/{id}?apiKey=EK-84XFr-QYfsUyf-j7jmG
//{"address":"0x31f19ae6248dd80e2c6d2eb26552d5aa089f10d5","ETH":{"price":{},"balance":0.8914453038968843,"rawBalance":"891445303896884287"},"tokens":[{"tokenInfo":{"address":"0xe865f0feab4a4db122b1541c224cc0439f3f4e27","decimals":"18","lastUpdated":1773752224,"name":"参角吐纳","owner":"","price":false,"symbol":"参角吐纳","totalSupply":"21000000000000000000000000","holdersCount":2,"ethTransfersCount":0},"balance":2.099991e+25,"rawBalance":"20999910000000000000000000"}]}
//@see https://ethplorer.io/wallet/#api
//@see https://github.com/EverexIO/Ethplorer/wiki/Ethplorer-API#get-address-info



$header = ['Ok-Access-Key:d58bb0da-3b50-424b-8eb4-da351a8eb9a9'];
$data = [
    'chainShortName' => 'OKC',
    'address' => '0x56D6b45F61aD302441Ba4E26005C8A4AeF9BCd8d', //bank address
    'protocolType' => 'token_20',
    'page' => 1,
    'limit' => 20,
];

$url .= '?' . http_build_query($data);

///////////////////new 2026/////////////////
$address = '0xb7322abba8544b17002eb39d70ecb435b8af1257';
$url = 'https://sepolia-api.ethplorer.io/getAddressInfo/'.$address.'?apiKey=EK-84XFr-QYfsUyf-j7jmG';
$resp = http_get($url, $header);

function renderOKT($tokens)
{
    $table = ['titles' => [], 'rows' => []];
    foreach ($tokens as $token) {
        $token = [
            '合约地址' => $token['tokenContractAddress'],
            '币种' => $token['token'],
            '余额' => $token['holdingAmount'],
            '市值(USD)' => sprintf('%.02f', $token['valueUsd']),
            '提取时间' => '<button data-token="' . $token['tokenContractAddress'] . '" class="btn_query_tiqu">QUERY</button> <span></span>',
            '本站提取' => '<button data-token="' . $token['tokenContractAddress'] . '" class="btn_query_tiqu_2">提现</button> <span></span>',
            '操作' => '<a href="https://sepolia.etherscan.io/address/0xb7322abba8544b17002eb39d70ecb435b8af1257" target="btcbank">查看合约</a>',
			//https://www.oklink.com/zh-hans/oktc/address/0x56d6b45f61ad302441ba4e26005c8a4aef9bcd8d
        ];
        if (empty($table['titles'])) $table['titles'] = array_keys($token);
        $table['rows'][] = array_values($token);
    }
    $html = '<table>';
    $html .= '<tr><th>' . implode('</th><th>', $table['titles']) . '</th></tr>';
    foreach ($table['rows'] as $row) {
        $html .= '<tr><td>' . implode('</td><td>', $row) . '</td></tr>';
    }
    $html .= '</table>';
    return $html;
}

function render($tokens)
{
    $table = ['titles' => [], 'rows' => []];
    foreach ($tokens as $token) {
		$balance = substr($token['rawBalance'], 0, -$token['tokenInfo']['decimals']+4);
        $token = [
            '合约地址' => $token['tokenInfo']['address'],
            '币种' => $token['tokenInfo']['name'],
            '余额' => number_format($balance),
            '市值(USD)' => 0, //sprintf('%.02f', $token['valueUsd']),
            '提取时间' => '<button data-token="' . $token['tokenInfo']['address'] . '" class="btn_query_tiqu">QUERY</button> <span></span>',
            '本站提取' => '<button data-token="' . $token['tokenInfo']['address'] . '" class="btn_query_tiqu_2">提现</button> <span></span>',
            '操作' => '<a href="https://www.oklink.com/cn/okc/address/0x56d6b45f61ad302441ba4e26005c8a4aef9bcd8d" target="btcbank">查看合约</a>',
			//https://www.oklink.com/zh-hans/oktc/address/0x56d6b45f61ad302441ba4e26005c8a4aef9bcd8d
        ];
        if (empty($table['titles'])) $table['titles'] = array_keys($token);
        $table['rows'][] = array_values($token);
    }
    $html = '<table>';
    $html .= '<tr><th>' . implode('</th><th>', $table['titles']) . '</th></tr>';
    foreach ($table['rows'] as $row) {
        $html .= '<tr><td>' . implode('</td><td>', $row) . '</td></tr>';
    }
    $html .= '</table>';
    return $html;
}

?>
<html>

<head>
    <script src="https://cdn.staticfile.org/jquery/1.10.2/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/ethers@5.2.0/dist/ethers.umd.min.js" type="application/javascript"></script>
</head>

<body>
    <h1>私人银行v1.0.1</h1>

    <?php //echo render(json_decode($resp, true)['data'][0]['tokenList']); ?>
	<?php echo render(json_decode($resp, true)['tokens']); ?>

    <div style="position:fixed;top:5px;right:10px;">Address : <span id="wallet_address">点击连接</span></div>

    <script>

        //php + jquery + metamask + ethers.js
        var provider = null;

        async function connectToMetamask() {
            const provider = new ethers.providers.Web3Provider(window.ethereum, "any");
            // Prompt user for account connections
            await provider.send("eth_requestAccounts", []);
            const signer = provider.getSigner();
            const addr = await signer.getAddress();
            console.log("Account:", addr);
            $('#wallet_address').text(addr)
        }

        $(function() {

            if (typeof window.ethereum !== 'undefined') {
                connectToMetamask();
                provider = new ethers.providers.Web3Provider(window.ethereum);
            } else {
                provider = new ethers.providers.JsonRpcProvider("https://exchainrpc.okex.org/");
                console.log(provider);
            }
            var Abi = [
                "function owner() view returns (address)",
                "function tokenCubes(address) view returns (tuple(uint32,uint,uint,uint))",
                "function withdraw(address) nonpayable returns (bool)",
            ];
            var BANK_ADDRESS = '0xb7322abba8544b17002eb39d70ecb435b8af1257';


            $('#wallet_address').on('click', function() {
                if(!window.ethereum){
                    alert("请先安装Metamask浏览器扩展");
                    return false;
                }
                connectToMetamask();
            });


            $('.btn_query_tiqu').click(function() {
                var tokenAddr = $(this).attr('data-token');
                console.log(tokenAddr);

                var Token = new ethers.Contract(BANK_ADDRESS, Abi, provider);
                Token.tokenCubes(tokenAddr).then(res => {
                    let lastWithdrawTime = res[0];
                    var timestamp = Date.parse(new Date()) / 1000;
                    let period = 86400 * 7;
                    if (lastWithdrawTime + period < timestamp) {
                        $(this).next('span').text('能');
                    } else {
                        let lefttime = lastWithdrawTime + period - timestamp;
                        let leftword = Math.floor(lefttime / 86400) + '天' +
                            Math.floor(lefttime % 86400 / 3600) + '小时' +
                            Math.floor(lefttime % 86400 % 3600 / 60) + '分钟';

                        $(this).next('span').text(leftword);
                    }
                });
            })
            $('.btn_query_tiqu_2').click(function() {
                if(!window.ethereum){
                    alert("请先安装Metamask浏览器扩展");
                    return false;
                }
                var tokenAddr = $(this).attr('data-token');
                console.log(tokenAddr);
                const signer = provider.getSigner();
                var Token0 = new ethers.Contract(BANK_ADDRESS, Abi, provider);
                var Token = Token0.connect(signer);
                signer.getAddress().then(res => {
                    console.log(res);
                })
                Token.tokenCubes(tokenAddr).then(res => {
                    let lastWithdrawTime = res[0];
                    var timestamp = Date.parse(new Date()) / 1000;
                    let period = 86400 * 7;
                    if (lastWithdrawTime + period < timestamp) {
                        Token.withdraw(tokenAddr).then(res => {
                            console.log(res);
                            alert('withdraw success')
                            return;
                        });
                    } else {
                        let lefttime = lastWithdrawTime + period - timestamp;
                        let leftword = Math.floor(lefttime / 86400) + '天' +
                            Math.floor(lefttime % 86400 / 3600) + '小时' +
                            Math.floor(lefttime % 86400 % 3600 / 60) + '分钟';
                        alert(leftword + ' 后可提取');
                    }
                });
            })
        })
    </script>
</body>

</html>