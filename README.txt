The idea here is to show what is needed depending on server state

index.html starts in 'admin login' mode, which leads to ./admin after login to configure and control things

When the admin has 'opened up sign in', index.html will show the sign in

After sign in is closed, index.html will switch to score entry

From admin login, ./admin/index.html shows a menu of options:
	Open/Close Sign in.
	Configuration.
	Status.
	Summary.

Sign in mode shows an input for last name. On submit it looks up any players with that name,
and returns a table of each, with options for pool (set to player default), course selection,
and incoming tag #, followed by a sign in button.
After the list of matching players is a button to register a new player.
The registration page asks for first name, last name, pool, then takes you to the sign in page for that new player.
After you sign in, it shows your starting whole number, and asks you to hand in your tag and $$$.

The admin/status page should be the default, shows the admin signed in users, 
allows them to edit all user info, and has a check box for 'paid'.
Users should be sorted by starting hole.
This page also allows the admin to adjust scores and shows scores as they are entered.

Once everyone is checked in, the admin 'closes' sign in, and the system switches to score entry mode.

Score entry mode shows an input box for last name, and a course selection and hole # input.
If the cours and hole # is set, it brings up all players on that card, and prompts for scores for each.
If the last name is entered, it looks up players with that last name, and prompts you to chose one.
Once one is chosen, or if there's only one, it takes you to the score entry for that player's card.

The admin/configuration screen allows you to configure options such as, hole assignment order, 
cash distribution, week #, divisions, # places to pay out for each division, etc.

The admin/summary page includes all the information needed after the scores are all entered:
	Top N players of each division, and payout amounts
	All players for the night ranked by score for tag handouts.


Notes from luke:
need to be able to check in a group for the general (either luke or themselves)
if not checking in as a group, get a 'waiting group', when the group is full, only then do you get the next hole.
figure out starting hole sequences in config and check in
