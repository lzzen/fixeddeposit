// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

import {Ownable} from "@openzeppelin/contracts/access/Ownable.sol";
import {ReentrancyGuard} from "@openzeppelin/contracts/utils/ReentrancyGuard.sol";
import {IERC20} from "@openzeppelin/contracts/token/ERC20/IERC20.sol";
import {SafeERC20} from "@openzeppelin/contracts/token/ERC20/utils/SafeERC20.sol";

/// @title FixedDepositVault
/// @notice A simple 3-day timelocked vault for BNB (native) and ERC20 on BSC.
///         Anyone can deposit; only the owner can withdraw after unlock.
/// @dev Each asset has its own unlock time. Every deposit resets that asset's unlock to now + 3 days.
contract FixedDepositVault is Ownable, ReentrancyGuard {
    using SafeERC20 for IERC20;

    uint64 public constant LOCK_DURATION = 3 days;
    address public constant NATIVE = address(0);

    // asset => unlock timestamp (seconds)
    mapping(address => uint64) public unlockTime;

    event DepositNative(address indexed from, uint256 amount, uint64 unlockAt);
    event DepositERC20(address indexed token, address indexed from, uint256 amount, uint64 unlockAt);
    event WithdrawNative(address indexed to, uint256 amount);
    event WithdrawERC20(address indexed token, address indexed to, uint256 amount);

    constructor(address initialOwner) Ownable(initialOwner) {}

    receive() external payable {
        _depositNative(msg.sender, msg.value);
    }

    /// @notice Deposit BNB (native).
    function depositNative() external payable {
        _depositNative(msg.sender, msg.value);
    }

    /// @notice Deposit ERC20.
    /// @param token ERC20 token address
    /// @param amount Amount to deposit (must be approved first)
    function depositERC20(address token, uint256 amount) external nonReentrant {
        require(token != address(0), "token=0");
        require(amount > 0, "amount=0");

        IERC20(token).safeTransferFrom(msg.sender, address(this), amount);

        uint64 unlockAt = uint64(block.timestamp + LOCK_DURATION);
        unlockTime[token] = unlockAt;

        emit DepositERC20(token, msg.sender, amount, unlockAt);
    }

    /// @notice Owner withdraw BNB after unlock.
    function withdrawNative(address payable to, uint256 amount) external onlyOwner nonReentrant {
        require(to != address(0), "to=0");
        require(block.timestamp >= unlockTime[NATIVE], "locked");
        require(amount <= address(this).balance, "insufficient");

        (bool ok, ) = to.call{value: amount}("");
        require(ok, "native transfer failed");

        emit WithdrawNative(to, amount);
    }

    /// @notice Owner withdraw ERC20 after unlock.
    function withdrawERC20(address token, address to, uint256 amount) external onlyOwner nonReentrant {
        require(token != address(0), "token=0");
        require(to != address(0), "to=0");
        require(block.timestamp >= unlockTime[token], "locked");

        IERC20(token).safeTransfer(to, amount);
        emit WithdrawERC20(token, to, amount);
    }

    function _depositNative(address from, uint256 amount) internal {
        require(amount > 0, "amount=0");

        uint64 unlockAt = uint64(block.timestamp + LOCK_DURATION);
        unlockTime[NATIVE] = unlockAt;

        emit DepositNative(from, amount, unlockAt);
    }
}

