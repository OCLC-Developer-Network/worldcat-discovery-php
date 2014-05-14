Running Tests
=============

Unit Tests
----------
::
   The library includes a suite of unit tests. These tests do not interact with the actual web service. They use mock responses located in the tests/mocks directory.
   They can be run from the command line.

.. code:: bash
   $ cd tests
   $ ../vendor/bin/phpunit --suite unitTests