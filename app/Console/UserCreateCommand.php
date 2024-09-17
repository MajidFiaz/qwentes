<?php

namespace App\Console;

use App\Controllers\UsersController;
use App\Models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class UserCreateCommand extends Command
{
    protected function configure(): void
    {
        parent::configure();

        $this->setName('user:create');
        $this->setDescription('to create a user account: givenName familyName email password');
        $this->addArgument('givenName', InputArgument::REQUIRED, 'The givenName of the user.');
        $this->addArgument('familyName', InputArgument::REQUIRED, 'The familyName of the user.');
        $this->addArgument('email', InputArgument::REQUIRED, 'The email of the user.');
        $this->addArgument('password', InputArgument::REQUIRED, 'The password of the user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $aa=[
            'givenName'=>$input->getArgument('givenName'),
            'familyName'=>$input->getArgument('familyName'),
            'email'=>$input->getArgument('email'),
            'password'=>$input->getArgument('password')
        ];

        $user= new User();

        $user->givenName = $input->getArgument('givenName');
        $user->familyName = $input->getArgument('familyName');
        $user->email = $input->getArgument('email');
        $user->password = password_hash($input->getArgument('password'), PASSWORD_BCRYPT);
        $user->save();
        if ($user) {
            $output->writeln(sprintf('<info>Successfully Created</info>'));
        }else{
            $output->writeln(sprintf('<error>Failed to Create</error>'));
        }

        // The error code, 0 on success
        return 0;
    }
}