# final_presentation

!SLIDE

# eCatan

## Daniel Garman
## Andrew Kleehammer
## Rob Tarlecki

!SLIDE left

# Design and Architecture

- PHP
- XML files to track users, games, and game state
- Xampp

!SLIDE left

# D & A, ctd.

- SetDice: resource generation
- CreateGameXML: saves state of game to schema validated XML file
- ResumeGame: validates information and parses
- BuildRoad, Settlement, and City checks:
	- valid player turn
	- has resources
	- valid position
	- valid boundary

!SLIDE left

# D & A, ctd.

- Main.php
- GameBoard.php
- determineSettlementTypeAndColor
- determineRoadTypeAndColor

!SLIDE left

# Rules
- Terrain Rules
    - Each square produces resources when their corresponding number is rolled
    - Get resources when you border that square, even if not your turn
    - Use resources to build settlements, cities, and roads

!SLIDE left

# Rules, ctd.
- Game Rules
    - 8 victory points wins
        - 1 victory point per settlement
        - 2 victory points per city

!SLIDE left

# Requirements
- Two player game
- User name and password
- New Game/Join Game/Rejoin Game
- Clickable board allows placement
- Ten minute optional force timeout
- Logs, after finish
- User stats
    - Win/Loss
    - History

!SLIDE left

# Security
- Locked down server
- No access to game xml files or user files
- Self-signed cert
- Validate all input
- Authorize and Authenticate
- Random dice rolling

!SLIDE left

# Security, ctd.
- Used htmlentities() to prevent XSS
- Never used eval() to prevent remote execution
- File manipulation never relies on user input
