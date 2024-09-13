# Workflow
* Listing of scopes I would need
* Fleshing out tests that I would need
* Creating v2 of the GetProducts class
* Work on tests

## Could of done better
* The dynamic odering in the legacy code was not working, I could have fixed it by figuring out where the ```$order``` variable was to replace the ```$orderby``` variable.
* The dynamic ordering was not completed, I could have made a scope that applied the ordering depending on a parameter passed to it.
* I could have looked into some laravel validation rules to make the validation more robust.
* I could have written alot more tests to cover the edge cases of the system.
* The ```StoreProduct``` model became a little bulky, I could have abstracted the scope methods into a trait/s, that could then be applied to other future models.
* The factory could have been adapted to create more complex and varied data by using faker.
* The tests could have been better if they refreshed and seeded the database before each test in a setUp method.
* Some custom Exceptions could have been created to handle the errors in a more robust way.

## Recommendations
* disabled_countries column in the store poducts can easily be exported to a seperate table with apropriate indexing and relationship leading to faster queries.
