// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

import "@openzeppelin/contracts/token/ERC20/ERC20.sol";
import "@openzeppelin/contracts/token/ERC20/extensions/ERC20Permit.sol";

contract WWfToken is ERC20, ERC20Permit {
    constructor() ERC20(unicode"永不梭哈", unicode"Never All-in") ERC20Permit(unicode"永不梭哈") {
        _mint(msg.sender, 21000000 * 10**decimals());
    }
}
