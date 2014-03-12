{
    "title": "Cheat Sheet - phpspec Matchers",
    "date": "2014-03-12",
    "excerpt": "This cheat sheet provides an overview of all the matchers that are supported by phpspec - including some that are currently not included in the documentation."
}

I want to [write a book about phpspec](https://leanpub.com/phpspec-getting-started), since there is not much documentation out there. Because of that, I am currently doing a lot of research an thought I would share some of it here.

This cheat sheet provides an overview of all the matchers that [are supported by phpspec](http://phpspec.net/cookbook/matchers.html) - including some that are currently _not_ included in the documentation.

The code for these matchers can be viewed on [Github](https://github.com/phpspec/phpspec/tree/master/src/PhpSpec/Matcher).

**All matchers can be prefixed with a 'not', as in `shouldNotBe()`.**

### All matchers

<table class="pure-table pure-table-bordered table-cheat-sheet">
<thead>
<tr>
  <th>Type</th>
  <th>Method</th>
  <th>PHP equivalent</th>
  <th>Description</th>
</tr>
</thead>
<tbody>
<tr>
  <td>Arrays</td>
  <td><code>shouldContain()</code></td>
  <td><code>in_array()</code></td>
  <td>Check that value is in array.</td>
</tr>
<tr>
  <td></td>
  <td><code>shouldHaveCount()</code></td>
  <td><code>count()</code></td>
  <td>Either the count of an array or a call() method on an object implementing the Countable interface.</td>
</tr>
<tr>
  <td></td>
  <td><code>shouldHaveKey()</code></td>
  <td><code>isset()</code> or <code>array_key_exists()</code></td>
  <td>Check that array has key.</td>
</tr>
<tr>
  <td>Comparison</td>
  <td><code>shouldBeLike()</code></td>
  <td><code>==</code></td>
  <td>Compare two variables/objects and confirm that they have the <strong>same value</strong>.</td>
</tr>
<tr>
  <td>Identity</td>
  <td><code>shouldBe()</code></td>
  <td><code>===</code></td>
  <td>Compare two variables/objects and confirm that they have the <strong>same value and type</strong>.</td>
</tr>
<tr>
  <td></td>
  <td><code>shouldBeEqualTo()</code></td>
  <td><code>===</code></td>
  <td><em>Same as above.</em></td>
</tr>
<tr>
  <td></td>
  <td><code>shouldEqual()</code></td>
  <td><code>===</code></td>
  <td><em>Same as above.</em></td>
</tr>
<tr>
  <td></td>
  <td><code>shouldReturn()</code></td>
  <td><code>===</code></td>
  <td><em>Same as above.</em></td>
</tr>
<tr>
  <td>ObjectState</td>
  <td><code>shouldBe*()</code></td>
  <td><code>is*()</code></td>
  <td><code>shouldBeAwesome()</code> will return the value of an <code>isAwesome()</code> method on the object.</td>
</tr>
<tr>
  <td></td>
  <td><code>shouldHave*()</code></td>
  <td><code>has*()</code></td>
  <td><code>shouldHaveAwesomeness()</code> will return the value of an <code>hasAwesomeness()</code> method on the object.</td>
</tr>
<tr>
  <td>Scalar</td>
  <td><code>shouldBeString()</code></td>
  <td><code>is_string()</code></td>
  <td>Works with all is_*() PHP standard functions, like <code>is_string()</code> or <code>is_null()</code> etc.</td>
</tr>
<tr>
  <td>Strings</td>
  <td><code>shouldEndWith()</code></td>
  <td><code>substr()</code></td>
  <td>Use <code>substr()</code> function to check end of string.</td>
</tr>
<tr>
  <td></td>
  <td><code>shouldStartWith()</code></td>
  <td><code>strpos()</code></td>
  <td>Use <code>strpos()</code> function to check beginning of string.</td>
</tr>
<tr>
  <td></td>
  <td><code>shouldMatch()</code></td>
  <td><code>preg_match()</code></td>
  <td>Compare with regex pattern.</td>
</tr>
<tr>
  <td>Throw</td>
  <td><code>shouldThrow()-&gt;during()</code></td>
  <td></td>
  <td>Check for an exception: <code>-&gt;shouldThrow('\Exception')</code> <code>-&gt;during('function', array('parameters'))</code></td>
</tr>
<tr>
  <td>Type</td>
  <td><code>shouldBeAnInstanceOf()</code></td>
  <td><code>instanceof</code></td>
  <td>Run the <code>instanceof</code> operator on an object to confirm it's type.</td>
</tr>
<tr>
  <td></td>
  <td><code>shouldHaveType()</code></td>
  <td><code>instanceof</code></td>
  <td><em>Same as above.</em></td>
</tr>
<tr>
  <td></td>
  <td><code>shouldImplement()</code></td>
  <td><code>instanceof</code></td>
  <td><em>Same as above.</em></td>
</tr>
<tr>
  <td></td>
  <td><code>shouldReturnAnInstanceOf()</code></td>
  <td><code>instanceof</code></td>
  <td><em>Same as above.</em></td>
</tr>
</tbody>
</table>

_Let me know if I missed one!_

### Custom matchers

It is possible, and indeed easy, to implement your own matchers. All you have to do is overwrite the `getMatchers()` method in your spec, which is inherited from `ObjectBehavior`. In this example, I will implement two matchers to verify that a value is either `true` or `false`.

```php
public function getMatchers()
    {
        return [
            'beTrue' => function($subject) {
                return $subject === true;
            },
            'beFalse' => function($subject) {
                return $subject === false;
            },
        ];
    }
```

With this code in our spec, we can do stuff like this:

```php
function it_is_true_and_false()
{
    $this->value->shouldBeTrue();

    $this->anotherValue->shouldBeFalse();

    // And because phpspec is awesome:

    $this->value->shouldNotBeTrue();

    $this->anotherValue->shouldNotBeFalse();
}
```

Easy, huh!