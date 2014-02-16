Description
-----------

So far this is a proof of concept. It shows that it could be nice (and isn't too difficult, as expected) to have comparison tables, based on information from the Stacks project.


Setup
-----

The setup is different from the one originally described. It works as follows:

* this repository contains both the base code and the instances
* 3 tables are created for each table by `database/create.py`, based on the standard format and extra fields (which are described in `database/prefix` where `prefix` stands for the prefix that is used for the 3 tables
* using `database/import.py` the JSON files in the directory for a given table are imported
* `table.php` contains a basic table, which can then be extended to take into acount the extra fields present in the database

People are free to fork this repository and / or propose changes, which will then be merged (if we like them).


Ideas for tables
----------------

Some ideas for tables:

1. properties of morphisms versus whether they are preserved under certain operations (base change, composition, fpqc descent, fppf descent, "spreading out", ...), see also Poonen's table in Rational points on varieties
2. properties of morphisms / objects versus schemes, algebraic spaces, stacks: a nice overview table of where you can find which property
3. properties of objects in derived categories of modules (perfectness, pseudo-coherence, tor-amplitude, boundedness, ...) versus whether they are local for a topology, preserved under pullback...
4. similar to the previous one, but just for sheaves of modules, not in the derived category
5. similar to the second, but now for "main theorems" such as Grothendieck existence, formal functions, cohomology and base change, Leray spectral sequence, projection formula, ...: we could see which things are missing, which things are not true, where the similar results are written down
6. comparison of properties for topologies (e.g. closed immersions correspond to exact pushforward)


General ideas
-------------

Some general ideas:

1. refer to EGA's (as in Poonen's table, relates to having more explicit references to the EGA's in the Stacks project too)
2. sorting things, based on location in the stacks project, being true or not, ...
3. more visual clues (colour?)
4. if we at some point have slogans in the Stacks project, these tables serve as an excellent reality check as the format of the slogans could be standardised for some of the tables
5. dependency check: when you click on a fact, show which other things depend on it (or it depends on)

To anyone who reads this: feel free to make suggestions.
