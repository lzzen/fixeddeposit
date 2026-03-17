// SPDX-License-Identifier: MIT
pragma solidity ^0.8.17;

// import "hardhat/console.sol";

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
 * 每周提取十分之一
 *
 * @title
 * @author
 * @notice
 */
contract BTCBankTenWeek {
    address payable public owner;

    constructor() payable {
        owner = payable(msg.sender);
    }

    /**
     * [BTC, time1, 10/+100/90]
     * @title
     * @author
     * @notice
     */
    struct WithdrawCube {
        uint32 lastWithdrawTime;
        uint withdrawAmount;
        uint newIncomeAmount;
        uint leftAmount;
    }
    mapping(IERC20 => WithdrawCube) public tokenCubes;

    function withdraw(address token_) external returns (bool) {
        // Uncomment this line, and the import of "hardhat/console.sol", to print a log in your terminal
        // console.log("Unlock time is %o and block timestamp is %o", unlockTime, block.timestamp);

        // require(block.timestamp >= unlockTime, "You can't withdraw yet");
        require(msg.sender == owner, "You aren't the owner");

        IERC20 token = IERC20(token_);
        WithdrawCube memory wdc0 = tokenCubes[token];
        // console.log(block.timestamp, wdc0.lastWithdrawTime);
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
        // if(tokenCubes[token].leftAmount > 0 ){
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

        // console.log(totalAmount,withdrawAmount,newIncomeAmount,leftAmount);

        // token.transfer(msg.sender, 1);
        // token.transfer(msg.sender, token.balanceOf(address(this)));
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
}
