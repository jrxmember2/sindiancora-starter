# Licenciamento Contratual Personalizado

O SindiÂncora não utiliza planos fixos.

Cada empresa tem uma licença com:

- quantidade de condomínios;
- quantidade de usuários internos;
- módulos liberados;
- vigência;
- valor;
- regras de bloqueio;
- possibilidade de excedente.

O serviço principal é:

```php
App\Services\Licensing\LicenseGuard
```

Métodos iniciais:

- `isActive`
- `canAccessModule`
- `canCreateCondominium`
- `canCreateInternalUser`
- `usage`
