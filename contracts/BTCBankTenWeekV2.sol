// SPDX-License-Identifier: MIT
pragma solidity ^0.8.17;

interface IERC20 {
    function totalSupply() external view returns (uint);

    function balanceOf(address account) external view returns (uint);

    function transfer(address recipient, uint amount) external returns (bool);

    function allowance(
        address owner,
        address spender
    ) external view returns (uint);

    function approve(address spender, uint amount) external returns (bool);

    function transferFrom(
        address sender,
        address recipient,
        uint amount
    ) external returns (bool);

    event Transfer(address indexed from, address indexed to, uint value);
    event Approval(address indexed owner, address indexed spender, uint value);
}

/**
 * 每周提取十分之一（支持 ERC20 和原生币 ETH）
 *
 * @title BTCBankTenWeekV2
 * @author
 * @notice 使用 call() 安全转账原生币
 */
contract BTCBankTenWeekV2 {
    address payable public owner;

    constructor() payable {
        owner = payable(msg.sender);
    }

    /**
     * [BTC, time1, 10/+100/90]
     */
    struct WithdrawCube {
        uint32 lastWithdrawTime;
        uint withdrawAmount;
        uint newIncomeAmount;
        uint leftAmount;
    }
    mapping(IERC20 => WithdrawCube) public tokenCubes;

    // 接收 ETH
    receive() external payable {}

    /**
     * ERC20 代币每周提取
     */
    function withdraw(address token_) external returns (bool) {
        require(msg.sender == owner, "You aren't the owner");

        IERC20 token = IERC20(token_);
        WithdrawCube memory wdc0 = tokenCubes[token];
        require(
            block.timestamp >= wdc0.lastWithdrawTime + 86400 * 7,
            "You can't withdraw yet"
        );

        uint totalAmount = token.balanceOf(address(this));
        if(totalAmount == 0){
            return false;
        }
        
        uint newIncomeAmount = 0;
        uint withdrawAmount = 0;
        uint leftAmount = 0;
        
        if (wdc0.lastWithdrawTime > 0) {
            newIncomeAmount = totalAmount - wdc0.leftAmount;
            withdrawAmount = wdc0.withdrawAmount < wdc0.leftAmount
                ? wdc0.withdrawAmount
                : wdc0.leftAmount;
            withdrawAmount += newIncomeAmount / 10;
        } else {
            newIncomeAmount = totalAmount;
            withdrawAmount = newIncomeAmount / 10;
        }
        leftAmount = totalAmount - withdrawAmount;

        token.transfer(msg.sender, withdrawAmount);

        WithdrawCube memory wdc = WithdrawCube({
            lastWithdrawTime: uint32(block.timestamp),
            withdrawAmount: withdrawAmount,
            newIncomeAmount: newIncomeAmount,
            leftAmount: leftAmount
        });

        tokenCubes[token] = wdc;

        return true;
    }

    /**
     * 原生币 ETH 每周提取（使用 call() 安全转账）
     */
    function withdrawETH() external returns (bool) {
        require(msg.sender == owner, "You aren't the owner");

        WithdrawCube memory wdc0 = tokenCubes[IERC20(address(0))];
        require(
            block.timestamp >= wdc0.lastWithdrawTime + 86400 * 7,
            "You can't withdraw yet"
        );

        uint totalAmount = address(this).balance;
        if(totalAmount == 0){
            return false;
        }

        uint newIncomeAmount = 0;
        uint withdrawAmount = 0;
        uint leftAmount = 0;

        if (wdc0.lastWithdrawTime > 0) {
            newIncomeAmount = totalAmount - wdc0.leftAmount;
            withdrawAmount = wdc0.withdrawAmount < wdc0.leftAmount
                ? wdc0.withdrawAmount
                : wdc0.leftAmount;
            withdrawAmount += newIncomeAmount / 10;
        } else {
            newIncomeAmount = totalAmount;
            withdrawAmount = newIncomeAmount / 10;
        }
        leftAmount = totalAmount - withdrawAmount;

        // 使用 call() 安全转账，避免重入和 gas limit 问题
        (bool success, ) = owner.call{value: withdrawAmount}("");
        require(success, "ETH transfer failed");

        WithdrawCube memory wdc = WithdrawCube({
            lastWithdrawTime: uint32(block.timestamp),
            withdrawAmount: withdrawAmount,
            newIncomeAmount: newIncomeAmount,
            leftAmount: leftAmount
        });

        tokenCubes[IERC20(address(0))] = wdc;

        return true;
    }
}
