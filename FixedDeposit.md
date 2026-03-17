# 定存功能

## 合约
- `contracts/FixedDepositVault.sol`
- **功能**：支持存入 BNB（原生币）或任意 ERC20；每次存入会将该资产的解锁时间更新为“当前时间 + 3 天”；**仅合约 owner 可在解锁后提取**。

## 第三方库（OpenZeppelin）
合约依赖：`@openzeppelin/contracts`

## 关键接口
- **存入 BNB**：`depositNative()`（或直接转账到合约地址触发 `receive()`）
- **存入 ERC20**：`depositERC20(token, amount)`（需先对合约 `approve`）
- **提取 BNB（仅 owner）**：`withdrawNative(to, amount)`（需 `unlockTime[address(0)]` 已到期）
- **提取 ERC20（仅 owner）**：`withdrawERC20(token, to, amount)`（需 `unlockTime[token]` 已到期）
