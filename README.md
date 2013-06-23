# Bandejão

API em PHP para obter os cardápios dos bandejões da USP de Butantã.

Para retornar o cardápio do restaurante da Química, por exemplo, basta escrever
```php
get('quimica');
```

Já para os bandejões da Física e da Prefeitura, fica:
```php
get('fisica,prefeitura');
```

Os restaurantes podem ser `central`, `fisica`, `prefeitura`, `quimica` ou `clube`.


### Opções

A função `get` assume um segundo parâmetro, opcional, de opções:
```php
get($restaurants, $options);
```

Onde `$options` é uma array com os possíveis parâmetros:

* `days`: array com os índices dos dias cujos cardápios serão retornados, variando de `0` (segunda-feira) até `6` (domingo). Caso seja `-1`, retorna o índice do dia atual;
* `meals`: array com os índices das refeições cujos cardápios serão retornados, assumindo `0` (almoço) ou `1` (jantar). Caso seja `-1`, retorna o índice da refeição atual;
* `time_format`: formato de tempo, segundo as [convenções em PHP] [date], para os dias da semana;
* `meal_format`: formato utilizado para as identificar as refeições, podendo ser `numeric`;
* `implode`: se `TRUE`, compacta os resultados em arrays de um elemento, apenas.


### Retornar saldo

Para visualizar o saldo disponível na carteirinha USP , basta escrever
```php
balance($nusp, $password);
```

Onde:

* `$nusp` é o número USP do usuário
* `$password` é a senha do usuário na página do [Rucard] [rucard]

 

[date]: http://php.net/manual/en/function.date.php
[rucard]: https://uspdigital.usp.br/rucard/‎