<?php
// Clase con métodos estáticos con utilidades que se podrán emplear en diversas partes del código
class Util {
  static function generarId() {
    // ID único a partir de: https://stackoverflow.com/questions/29235481/generate-a-readable-random-unique-id
    return join('-', str_split(bin2hex(openssl_random_pseudo_bytes(10)), 5));
  }

  
}