<?php

namespace App\Common;

class SystemModules {

    //PERMISSÕES
    private static $permissions = [
        "Funcionários",
        "Clientes",
        "Gerentes",
        "Empresas",
        "Entrada",
        "Saída",
        "Relatórios"
    ];

    public static function getPermissions(){
        return self::$permissions;
    }


/* MODELOS DE MODULOS DO MENU LATERAL
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

//MODULO SIMPLES
 'Dashboard' => 
        [
            'label' => 'Dashboard', <-- VAI PEGAR PELO LABEL
            'link' => URL.'/painel',
            'icon' => 'fas fa-tachometer-alt' <-- ICONE DA OPÇÃO NO MENU
        ]

//MODULO COM SUBMENU
'users' => [
            'label' => 'Usuários',
            'icon' => 'fas fa-users',  <-- ICONE DA OPÇÃO NO MENU
            'subsections' => [
                'name' => 'Layouts-users',
                'icon' => 'fas fa-caret-down',
                'items' => [
                    [
                        'label' => 'Funcionários', <-- VAI PEGAR PELO LABEL
                        'link' => URL.'/painel/user'
                    ],  
                    [
                        'label' => 'Clientes',  <-- VAI PEGAR PELO LABEL
                        'link' => URL.'/painel/clientes'
                    ]           
            
                ]
            ]
        ]

-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/



        private static $modules = [
        
        'Dashboard' => [
            'label' => 'Dashboard',
            'link' => URL.'/painel',
            'icon' => 'fas fa-tachometer-alt'
        ],
        'users' => [
            'label' => 'Usuários',
            'icon' => 'fas fa-users',
            'subsections' => [
                'name' => 'Layouts-users',
                'icon' => 'fas fa-caret-down',
                'items' => [
                    [
                        'label' => 'Funcionários',
                        'link' => URL.'/painel/user'
                    ],  
                    [
                        'label' => 'Clientes',
                        'link' => URL.'/painel/clientes'
                    ]           
            
                ]
            ]
        ],
        'Parcerias' => [
            'label' => 'Parcerias',
            'icon' => 'fa-regular fa-building',   
            'subsections' => [
                'name' => 'Layouts-parcerias',
                'icon' => 'fas fa-caret-down',
                'items' => [            
                    [
                        'label' => 'Gerentes',
                        'link' => URL.'/painel/gerentes'
                    ],
                    [
                        'label' => 'Empresas',
                        'link' => URL.'/painel/empresa'
                    ]
                ]
            ]
        ],
        'Financeiro' => [
            'label' => 'Financeiro',
            'icon' => 'fa-solid fa-coins',
            'subsections' => [
                'name' => 'Layouts-Financeiro',
                'icon' => 'fas fa-caret-down',
                'items' => [
                    [
                        'label' => 'Entrada',
                        'link' => URL.'/painel/caixa/entrada'
                    ],
                    [
                        'label' => 'Saída',
                        'link' => URL.'/painel/caixa/saida'
                    ],
                    [
                        'label' => 'Relatórios',
                        'link' => URL.'/painel/caixa/relatorio'
                    ]
                ]
            ]
        ],
        'Termos de Uso' => [
            'label' => 'Termos de Uso',
            'link' => URL.'/painel/termos-de-uso',
            'icon' => 'fa-solid fa-file-circle-check'
        ]

    ];

    public static function getModules(){

        return Self::$modules;
    }

}
