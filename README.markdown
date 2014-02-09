Description
-----------

So far this is a proof of concept. It shows that it could be nice (and isn't too difficult, as expected) to have comparison tables, based on information from the Stacks project.


Setup
-----

We envision more tables (see later for some suggestions). To do so we would like to have a common base, and instantiate this. Some remarks concerning a possible setup for this:

* create a `stacks-table` base repository, that has all the required main things
  - managing three tables: column headers, row headers and then the relations
  - JSON import, without any frills, but with helper code to check changes to fields that will be added in the forks (see later)
  - PHP pulls stuff from the database
  - JS code for showing standard things (i.e. previewing tags, show references to EGA's etc, sort the table)
  - configuration: give a prefix for the tables we'd have to use to keep multiple tables (see later) separate
* fork this `stacks-table` repository into `morphism-properties-preservation-table` (that would be the one with the functionality currently in this project) and add the required functionality:
  - handle the table-specific fields in the input (i.e. in the current situation we have tags associated to row headers and relations, but that might be different for other tables)
  - output table based on the extra fields

This seems like the best approach: we'd be able to merge changes upstream (i.e. the base repository) as we go along, we can easily instantiate new tables by just forking, changing the config a little and (if required) adding fields to the input and output.

At first it seemed like this would be a lot of work, but once you think about it, it's not too difficult: we'd have a `Table` class, overload it in the forks (hence we can easily integrate it into the real `stacks-website` project).


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

To anyone who reads this: feel free to make suggestions.
