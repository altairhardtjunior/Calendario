# Calendario
Calendário de eventos (Full Calendar)

Sistema de agendamento e compartilhamento de eventos e atividades para empresa de pequeno porte. Cria e edita um Calendário de eventos em que os usuários podem criar e gerenciar a agenda de atividades.



<img src="https://user-images.githubusercontent.com/33841428/144291736-a6cbb861-436d-4132-9630-cf3e32e34443.png" style="width: 500px;">
<img src="https://user-images.githubusercontent.com/33841428/144293523-1ca4430e-741f-42d1-887a-46d4a263c0ca.png" style="width: 400px;">

# Configuração do Ambiente

Utilize um software que emule um servidor web e suba os serviços de servidor apache, mySql e PHP (Sugiro utilizar o XAMPP<img src="https://user-images.githubusercontent.com/33841428/144295720-a105376a-e5c6-450e-910d-72be58916495.png" style="width: 50px;">). 

Em seguida copie a pasta calendário na pasta raiz do seu servidor web, crie um banco de dados com o nome que preferir, não esquecendo de alterar também no arquivo "CLASSES/conexao.php". 


Este banco de dados deve possuir uma tabela com o nome "events" com os seguintes campos: id, title, start e end.
