VotingBundle for Symfony2
============================

A port of the awesome VotingApi Drupal module. https://drupal.org/project/votingapi

Vote Value Types
----------------
**Percent**
    - Votes in a specific range. Values are stored in a 1-100 range, but can be represented as any scale when shown to the user.

**Points**
    - Votes that contribute points/tokens/karma towards a total. May be positive or negative.

Methods
-------
**Count**
    - The number of votes cast for a given piece of content.
**Average**
    - The average vote cast on a given piece of content.
**Sum**
    - The total of all votes for a given piece of content.
