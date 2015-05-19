<?php
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\VarDumper\Caster\Caster;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Graviton\Rql\Parser;

/**
 */
class XiagTestCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('xiag:test')
            ->setDescription('Xiag RQL test');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cloner = new VarCloner();
        $cloner->setMaxItems(100);
        $cloner->setMaxString(100);

        $dumper = new CliDumper();
        $dumpString = function ($value) use ($dumper, $cloner) {
            $stream = fopen('php://memory', 'w+');
            $dumper->dump($cloner->cloneVar($value, Caster::EXCLUDE_VERBOSE), $stream);

            rewind($stream);
            return stream_get_contents($stream);
        };

        $table = new Table($output);
        foreach (require(__DIR__ . '/../testdata.php') as $testName => $testData) {
            $table->addRow([
                '<comment>Test</comment>',
                '<info>' . $testName . '</info>',
            ]);
            $table->addRow(new TableSeparator());

            $table->addRow([
                '<comment>RQL</comment>',
                $testData['rql'],
            ]);
            $table->addRow(new TableSeparator());

            $table->addRow([
                '<comment>Is valid</comment>',
                $testData['valid'] ? '<fg=green>true</fg=green>' : '<fg=red>false</fg=red>',
            ]);
            $table->addRow(new TableSeparator());

            try {
                $table->addRow([
                    '<comment>Result</comment>',
                    $dumpString(Parser::createParser($testData['rql'])->getAST()),
                ]);
            } catch (\Exception $e) {
                $table->addRow([
                    '<comment>Result</comment>',
                    $dumpString($e),
                ]);
            }
            $table->addRow(new TableSeparator());

            $table->addRow([
                new TableCell("\n\n", ['colspan' => 2]),
            ]);
            $table->addRow(new TableSeparator());
        }
        $table->render();
    }
}
