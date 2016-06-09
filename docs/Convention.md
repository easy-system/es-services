Conventions
===========

1. The service names are case-sensitive. The service `Foo` and the service `foo`
   is not the same.
2. The name of service must use a point as separator between the parts of a 
   namespace:
   ```
       $services->set('Foo.Bar.Baz', 'Foo\\Bar\\Baz');
   ```
