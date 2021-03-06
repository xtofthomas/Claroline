<?php

namespace Claroline\SecurityBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DropAclCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('claroline:security:drop_acl')
             ->setDescription('Drops ACL tables in the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getContainer()->get('security.acl.dbal.connection');
        $sm = $connection->getSchemaManager();
        $tableNames = $sm->listTableNames();
        $tables = array(
            'entry_table_name' => $this->getContainer()->getParameter('security.acl.dbal.entry_table_name'),
            'class_table_name' => $this->getContainer()->getParameter('security.acl.dbal.class_table_name'),
            'sid_table_name'   => $this->getContainer()->getParameter('security.acl.dbal.sid_table_name'),
            'oid_ancestors_table_name' => $this->getContainer()->getParameter('security.acl.dbal.oid_ancestors_table_name'),
            'oid_table_name'   => $this->getContainer()->getParameter('security.acl.dbal.oid_table_name'),
        );

        $absentTables = 0;

        foreach ($tables as $table)
        {
            if (in_array($table, $tableNames, true)) 
            {
                $connection->exec("DROP TABLE {$table}");
            }
            else
            {
                ++$absentTables;
            }
        }

        if ($absentTables == 5)
        {
            $output->writeln('No ACL tables were found.');
        }
        else
        {
            $output->writeln('ACL tables have been successfully deleted.');
        }
    }
}
