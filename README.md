# TempPerms
- The TempPermissions plugin allows you to create vouchers in the form of paper items that grant temporary or permanent permissions to players.

- You use the /tc command followed by the permission name and optionally the duration time. If you don't specify a time, the permission will be permanent. Time formats are: 30s for seconds, 15m for minutes, 2h for hours, 7d for days.

- When you execute the command, you receive a paper item with the voucher name in purple color, displaying the permission and duration in its description. When right-clicking with the paper, the permission is automatically activated for the player using it.

- Permissions are saved in an SQLite database, so they persist between server restarts. Temporary permissions expire automatically when the time is up, and there's a system that checks every second if any permission has expired to remove it.

- It's useful for creating reward systems, selling temporary VIP permissions, special events, or any situation where you need to give permissions in a controlled and temporary way to players.
