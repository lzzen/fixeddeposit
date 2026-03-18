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
$address = '0x73cfcac47c6c2a7d74459cdb0aae980ac925aa85';
$url = 'https://api.binplorer.com/getAddressInfo/'.$address.'?apiKey=EK-84XFr-QYfsUyf-j7jmG';
$resp = http_get($url, $header);
// print_r($resp);

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

//{"address":"0xd7349e91868ce73401b584000f04fcc087296ddb","ETH":{"price":{"rate":647.0953841083967,"diff":-3.5,"diff7d":-0.02,"ts":1773851160,"marketCapUsd":88236494354.33638,"availableSupply":136357786.69,"volume24h":1783662518.0848389,"volDiff1":500.9971318549357,"volDiff7":-12.243527952265666,"volDiff30":-40.36711644004981,"diff30d":5.3129442817476615},"balance":0,"rawBalance":"0"},"contractInfo":{"creatorAddress":"0x31f19ae6248dd80e2c6d2eb26552d5aa089f10d5","creationTransactionHash":"0xfd253d70e98355a33d0900a03787453cd5ba8fb2f435950e1d616aa5e426ad03","creationTimestamp":1773766736},"tokens":[{"tokenInfo":{"address":"0x7130d2a12b9bcbfae4f2634d864a1ee1ce3ead9c","decimals":"18","lastUpdated":1773851945,"name":"Binance Bitcoin","owner":"0xf68a4b64162906eff0ff6ae34e2bb1cd42fef62d","price":{"rate":71496,"diff":-3.03,"diff7d":0,"ts":1773849959,"marketCapUsd":0,"availableSupply":0,"volume24h":59734051.316728234,"volDiff1":-43.0693477171886,"volDiff7":-15.280794887106481,"volDiff30":-35.205587066048565,"diff30d":3.9997989507397875,"currency":"USD"},"symbol":"BTCB","totalSupply":"65300969964784133902393","holdersCount":1443434,"ethTransfersCount":0},"balance":39173270285466,"rawBalance":"39173270285466"},{"tokenInfo":{"address":"0xa35fe789a61f47c2c65693ece9a7080aa1a63332","decimals":"18","lastUpdated":1773780059,"name":"财富风口(Wealth Windfall)","owner":"","price":false,"symbol":"财富风口","totalSupply":"1000000000000000000000000000","holdersCount":87,"ethTransfersCount":0},"balance":10000000000000000000,"rawBalance":"10000000000000000000"}]}
function rendEthRow($eth, $name='ETH'){
    // $balance = substr($eth['rawBalance'], 0, 6) * pow(10,(strlen($eth['rawBalance'])-6-$token['tokenInfo']['decimals']));
    $balance = $eth['balance'];
    $value = $balance * $eth['price']['rate'];
    $tkaddr= '0x0000000000000000000000000000000000000000';
    $row = [
    '合约地址' => $tkaddr,
    '币种' => $name,
    '余额' => number_format($balance, 6),
    '市值(USD)' => sprintf('%.02f', $value),
    '提取时间' => '<button data-token="' . $tkaddr . '" class="btn_query_tiqu">QUERY</button> <span></span>',
    '本站提取' => '<button data-iseth="1" data-token="' . $tkaddr . '" class="btn_query_tiqu_2">提现</button> <span></span>',
    '操作' => '<a href="https://bscscan.com/address/0x73cfcac47c6c2a7d74459cdb0aae980ac925aa85#readContract" target="btcbank">查看合约</a>',
    ];
    return $row;
}

function render($data)
{
    $tokens = $data['tokens'];
    $row = rendEthRow($data['ETH'], 'BNB');
    $table = ['titles' => array_keys($row), 'rows' => [$row]];
    foreach ($tokens as $token) {
// 		$balance = substr($token['rawBalance'], 0, -$token['tokenInfo']['decimals']+4)/10000;
		$balance = substr($token['rawBalance'], 0, 6) * pow(10,(strlen($token['rawBalance'])-6-$token['tokenInfo']['decimals']));
// 		var_dump(strlen($token['rawBalance'])-6-$token['tokenInfo']['decimals']);
        $token = [
            '合约地址' => $token['tokenInfo']['address'],
            '币种' => $token['tokenInfo']['name'],
            '余额' => number_format($balance, 6),
            '市值(USD)' => $token['tokenInfo']['price'] ? number_format($token['tokenInfo']['price']['rate'] * $balance,2) : 0, //sprintf('%.02f', $token['valueUsd']),
            '提取时间' => '<button data-token="' . $token['tokenInfo']['address'] . '" class="btn_query_tiqu">QUERY</button> <span></span>',
            '本站提取' => '<button data-token="' . $token['tokenInfo']['address'] . '" class="btn_query_tiqu_2">提现</button> <span></span>',
            '操作' => '<a href="https://bscscan.com/address/0x73cfcac47c6c2a7d74459cdb0aae980ac925aa85#readContract" target="btcbank">查看合约</a>',
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
    <script src="jquery.min.js"></script>
    <script src="ethers.umd.min.js" type="application/javascript"></script>
</head>

<body>
    <h1>私人银行v1.2.0</h1>
	<div>
	    <textarea><?=$resp?></textarea>
	</div>
	
    <?php //echo render(json_decode($resp, true)['data'][0]['tokenList']); ?>
	<?php echo render(json_decode($resp, true)); ?>
	


    <div style="position:fixed;top:5px;right:10px;text-align:right;">
        <div>
            Network: <span id="network_name">未连接</span>
            <select id="network_select" style="padding:5px 10px;cursor:pointer;">
                <option value="">选择网络</option>
                <option value="1">Ethereum Mainnet</option>
                <option value="11155111">Sepolia Testnet</option>
                <option value="56">BSC Mainnet</option>
                <option value="137">Polygon</option>
                <option value="42161">Arbitrum</option>
                <option value="10">Optimism</option>
                <option value="43114">Avalanche</option>
                <option value="250">Fantom</option>
            </select>
        </div>
        <div style="margin-top:5px;">Address : <span id="wallet_address" style="cursor:pointer;">点击连接</span></div>
    </div>



    <div style="position:fixed;bottom:10px;text-align:center;width:100%;color:#ccc;font-size:8px"><b>Bank Contract Address: </b><?=$address?></div>
    
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
            $('#wallet_address').text(addr);
            
            // 获取当前网络
            const network = await provider.getNetwork();
            updateNetworkDisplay(network);
        }
        
        function updateNetworkDisplay(network) {
            const networkNames = {
                1: 'Ethereum Mainnet',
                11155111: 'Sepolia Testnet',
                56: 'BSC Mainnet',
                97: 'BSC Testnet',
                137: 'Polygon',
                80001: 'Polygon Mumbai',
                42161: 'Arbitrum',
                421614: 'Arbitrum Sepolia',
                10: 'Optimism',
                11155420: 'Optimism Sepolia',
                43114: 'Avalanche',
                43113: 'Avalanche Fuji',
                250: 'Fantom',
                4002: 'Fantom Testnet'
            };
            const name = networkNames[network.chainId] || `Chain ID: ${network.chainId}`;
            $('#network_name').text(name);
        }
        
        async function switchToNetwork(chainId) {
            try {
                await window.ethereum.request({
                    method: 'wallet_switchEthereumChain',
                    params: [{ chainId: '0x' + chainId.toString(16) }],
                });
            } catch (switchError) {
                // 如果网络不存在，尝试添加
                if (switchError.code === 4902) {
                    const chainConfigs = {
                        56: {
                            chainId: '0x38',
                            chainName: 'Binance Smart Chain',
                            rpcUrls: ['https://bsc-dataseed.binance.org'],
                            blockExplorerUrls: ['https://bscscan.com']
                        },
                        137: {
                            chainId: '0x89',
                            chainName: 'Polygon',
                            rpcUrls: ['https://polygon-rpc.com'],
                            blockExplorerUrls: ['https://polygonscan.com']
                        },
                        42161: {
                            chainId: '0xa4b1',
                            chainName: 'Arbitrum One',
                            rpcUrls: ['https://arb1.arbitrum.io/rpc'],
                            blockExplorerUrls: ['https://arbiscan.io']
                        },
                        10: {
                            chainId: '0xa',
                            chainName: 'Optimism',
                            rpcUrls: ['https://mainnet.optimism.io'],
                            blockExplorerUrls: ['https://optimistic.etherscan.io']
                        },
                        43114: {
                            chainId: '0xa86a',
                            chainName: 'Avalanche',
                            rpcUrls: ['https://api.avax.network/ext/bc/C/rpc'],
                            blockExplorerUrls: ['https://snowtrace.io']
                        },
                        250: {
                            chainId: '0xfa',
                            chainName: 'Fantom Opera',
                            rpcUrls: ['https://rpc.ftm.tools'],
                            blockExplorerUrls: ['https://ftmscan.com']
                        }
                    };
                    if (chainConfigs[chainId]) {
                        try {
                            await window.ethereum.request({
                                method: 'wallet_addEthereumChain',
                                params: [chainConfigs[chainId]],
                            });
                        } catch (addError) {
                            console.error('Failed to add network:', addError);
                        }
                    }
                } else {
                    console.error('Failed to switch network:', switchError);
                }
            }
        }

        $(function() {

            if (typeof window.ethereum !== 'undefined') {
                connectToMetamask();
                provider = new ethers.providers.Web3Provider(window.ethereum);
            } else {
                provider = new ethers.providers.JsonRpcProvider("https://exchainrpc.okex.org/");
                console.log(provider);
            }
            // var Abi = [
            //     "function owner() view returns (address)",
            //     "function tokenCubes(address) view returns (tuple(uint32,uint,uint,uint))",
            //     "function withdraw(address) nonpayable returns (bool)",
            //     "function withdrawETH() nonpayable returns (bool)",
            // ];
            var Abi = [{"inputs":[],"stateMutability":"payable","type":"constructor"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address payable","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"contract IERC20","name":"","type":"address"}],"name":"tokenCubes","outputs":[{"internalType":"uint32","name":"lastWithdrawTime","type":"uint32"},{"internalType":"uint256","name":"withdrawAmount","type":"uint256"},{"internalType":"uint256","name":"newIncomeAmount","type":"uint256"},{"internalType":"uint256","name":"leftAmount","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"token_","type":"address"}],"name":"withdraw","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"withdrawETH","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"stateMutability":"payable","type":"receive"}];
            
            var BANK_ADDRESS = '0xb7322abba8544b17002eb39d70ecb435b8af1257'; //必须写死 这是固定的


            $('#wallet_address').on('click', function() {
                if(!window.ethereum){
                    alert("请先安装Metamask浏览器扩展");
                    return false;
                }
                connectToMetamask();
            });
            
            $('#network_select').on('change', function() {
                const chainId = parseInt($(this).val());
                if(!chainId) return;
                
                if(!window.ethereum){
                    alert("请先安装Metamask浏览器扩展");
                    return false;
                }
                switchToNetwork(chainId);
            });
            
            // 监听网络变化
            if (window.ethereum) {
                window.ethereum.on('chainChanged', async () => {
                    const provider = new ethers.providers.Web3Provider(window.ethereum);
                    const network = await provider.getNetwork();
                    updateNetworkDisplay(network);
                    location.reload();
                });
            }


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
                var isETH = $(this).attr('data-iseth');
                console.log(tokenAddr);
                const signer = provider.getSigner();
                var Token0 = new ethers.Contract(BANK_ADDRESS, Abi, provider);
                var Token = Token0.connect(signer);
                signer.getAddress().then(async (userAddr) => {
                    console.log('User Address:', userAddr);
                    
                    // 检查是否为 Owner
                    try {
                        const ownerAddr = await Token.owner();
                        console.log('Owner Address:', ownerAddr);
                        
                        if (userAddr.toLowerCase() !== ownerAddr.toLowerCase()) {
                            alert('只有合约 Owner 能提取');
                            return;
                        }
                        
                        // 检查提取时间和执行提取
                        Token.tokenCubes(tokenAddr).then(async (res) => {
                            let lastWithdrawTime = res[0];
                            var timestamp = Date.parse(new Date()) / 1000;
                            let period = 86400 * 7;
                            if (lastWithdrawTime + period < timestamp) {
                                if(isETH == 1){
                                    //withdraw ETH
                                    try {
                                        const tx = await Token.withdrawETH({
                                            gasLimit: 3000000
                                        });
                                        console.log('tx hash:', tx.hash);
                                        const receipt = await tx.wait();
                                        console.log('receipt:', receipt);
                                        alert('withdraw success');
                                    } catch (error) {
                                        console.error('Error:', error);
                                        alert('Error: ' + error.message);
                                    }
                                }else{
                                    //withdraw ERC20
                                    try {
                                        const tx = await Token.withdraw(tokenAddr, {
                                            gasLimit: 3000000
                                        });
                                        console.log('tx hash:', tx.hash);
                                        const receipt = await tx.wait();
                                        console.log('receipt:', receipt);
                                        alert('withdraw success');
                                    } catch (error) {
                                        console.error('Error:', error);
                                        alert('Error: ' + error.message);
                                    }
                                }
                            } else {
                                let lefttime = lastWithdrawTime + period - timestamp;
                                let leftword = Math.floor(lefttime / 86400) + '天' +
                                    Math.floor(lefttime % 86400 / 3600) + '小时' +
                                    Math.floor(lefttime % 86400 % 3600 / 60) + '分钟';
                                alert(leftword + ' 后可提取');
                            }
                        });
                    } catch (error) {
                        console.error('Error getting owner:', error);
                        alert('Error: ' + error.message);
                    }
                });
            })
        })
    </script>
</body>

</html>