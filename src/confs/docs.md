** Configurable injectable interfaces

to add a dependency to the container, you can create a  file inside this folder and add the dependency in the following format

return [
  'dependencyName' => [
    'class' => VeryCoolPhpClass::Class,
    'params' => [
      'params' => 'for the constructor',
    ],
    'setupFunction' => function setup($params) {
      optional function called after delayed initialization
    }
  ]
]