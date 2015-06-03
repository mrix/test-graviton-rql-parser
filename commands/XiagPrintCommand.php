<?php
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Graviton\Rql\Lexer;
use Graviton\Rql\Parser;

/**
 */
class XiagPrintCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('xiag:print')
            ->setDescription('Xiag RQL print')
            ->addArgument(
                'rql',
                InputArgument::REQUIRED,
                'RQL query'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rql = $input->getArgument('rql');

        $output->getFormatter()->setStyle('token', $this->createTokenOutputStyle());

        $lexer = new Lexer();
        $lexer->setInput($rql);

        $table = new Table($output);
        $table->setHeaders([
            'Token',
            'Type',
            'Expression',
        ]);
        while ($lexer->moveNext()) {
            $token = $lexer->lookahead;
            $next = $lexer->glimpse();
            if ($next === null) {
                $next = [
                    'value' => '',
                    'type'  => 0,
                    'position' => strlen($rql),
                ];
            }

            $table->addRow([
                'token' => $token['value'],
                'type'  => $this->getTokenName($token['type']),
                'text'  => implode('', [
                    substr($rql, 0, $token['position']),
                    '<token>',
                    substr($rql, $token['position'], $next['position'] - $token['position']),
                    '</token>',
                    substr($rql, $next['position']),
                ]),
            ]);
        }
        $table->render();
    }

    /**
     * @return OutputFormatterStyle
     */
    protected function createTokenOutputStyle()
    {
        return new OutputFormatterStyle('black', 'yellow');
    }

    protected function getTokenName($type)
    {
        static $map = [
            Lexer::T_NONE => 'T_NONE',
            Lexer::T_INTEGER => 'T_INTEGER',
            Lexer::T_STRING => 'T_STRING',
            Lexer::T_OPEN_BRACKET => 'T_OPEN_BRACKET',
            Lexer::T_CLOSE_BRACKET => 'T_CLOSE_BRACKET',
            Lexer::T_CLOSE_PARENTHESIS => 'T_CLOSE_PARENTHESIS',
            Lexer::T_OPEN_PARENTHESIS => 'T_OPEN_PARENTHESIS',
            Lexer::T_COMMA => 'T_COMMA',
            Lexer::T_DOT => 'T_DOT',
            Lexer::T_SLASH => 'T_SLASH',
            Lexer::T_SINGLE_QUOTE => 'T_SINGLE_QUOTE',
            Lexer::T_DOUBLE_QUOTE => 'T_DOUBLE_QUOTE',
            Lexer::T_EQ => 'T_EQ',
            Lexer::T_NE => 'T_NE',
            Lexer::T_AND => 'T_AND',
            Lexer::T_OR => 'T_OR',
            Lexer::T_LT => 'T_LT',
            Lexer::T_GT => 'T_GT',
            Lexer::T_LTE => 'T_LTE',
            Lexer::T_GTE => 'T_GTE',
            Lexer::T_SORT => 'T_SORT',
            Lexer::T_PLUS => 'T_PLUS',
            Lexer::T_MINUS => 'T_MINUS',
            Lexer::T_LIKE => 'T_LIKE',
            Lexer::T_LIMIT => 'T_LIMIT',
            Lexer::T_IN => 'T_IN',
            Lexer::T_OUT => 'T_OUT',
            Lexer::T_COLON => 'T_COLON',
        ];

        return $map[$type];
    }
}
